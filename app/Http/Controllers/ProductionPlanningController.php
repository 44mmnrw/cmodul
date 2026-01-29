<?php

namespace App\Http\Controllers;

use App\Models\Detail;
use App\Models\ProductionOrder;
use App\Models\ProductionOrderStatus;
use App\Models\Stock;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionPlanningController extends Controller
{
    /**
     * Страница планирования производства с расчётом потребностей
     */
    public function index()
    {
        // Получить статус "Утверждено"
        $approvedStatus = ProductionOrderStatus::where('name', 'like', '%утвержд%')->orWhere('name', 'like', '%approved%')->first();
        
        // Получить все производственные заказы со статусом "Утверждено"
        $orders = ProductionOrder::query();
        
        if ($approvedStatus) {
            $orders->where('status_id', $approvedStatus->id);
        }
        
        $orders = $orders
            ->with([
                'order',
                'product' => function ($query) {
                    $query->with('stock', 'componentsInConfiguration.stock');
                },
                'orderStatus'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        // Группировать по order_id
        $groupedOrders = $orders->groupBy('order_id')->map(function ($group) {
            return [
                'order_id' => $group->first()->order_id,
                'created_at' => $group->first()->created_at,
                'planned_date' => $group->first()->planned_date,
                'status_id' => $group->first()->status_id,
                'status' => $group->first()->orderStatus,
                'order_items' => $group,
                'positions_count' => $group->count(),
                'total_quantity' => $group->sum('quantity_ordered'),
            ];
        })->sortByDesc('created_at')->values();

        return view('production.planning', [
            'orders' => $groupedOrders,
        ]);
    }

    /**
     * Получить анализ потребности для выбранных заказов (JSON API)
     */
    public function analyzeRequirements(Request $request)
    {
        $orderIds = $request->input('order_ids', []);
        
        if (empty($orderIds)) {
            return response()->json([
                'selected_count' => 0,
                'total_required' => 0,
                'can_assemble' => 0,
                'need_to_produce' => 0,
                'components' => []
            ]);
        }

        // Получить выбранные производственные заказы
        $selectedOrders = ProductionOrder::whereIn('id', $orderIds)
            ->with('product.stock', 'product.componentsInConfiguration.stock', 'orderStatus')
            ->get();

        $totalRequired = 0;
        $totalInStock = 0;
        $totalNeedProduce = 0;
        
        // Агрегация компонентов по их ID
        // Для каждого компонента считаем максимум требуемого по всем заказам
        $componentRequirements = [];

        foreach ($selectedOrders as $order) {
            $product = $order->product;
            
            if (!$product) {
                continue;
            }

            // Type 1: Конфигурация - разбираем на компоненты
            if ($product->product_type_id == 1) {
                $orderQty = $order->quantity_ordered;
                
                // Получить все компоненты этой конфигурации
                foreach ($product->componentsInConfiguration as $component) {
                    $requiredPerConfig = $component->pivot->quantity;
                    // Рассчитываем требуемое количество компонента для этого заказа
                    // Пример: если нужно 10 шкафов, а каждый требует 1 компонента → 10 × 1 = 10 шт
                    $requiredForThisOrder = $orderQty * $requiredPerConfig;
                    
                    $componentId = $component->id;
                    
                    if (!isset($componentRequirements[$componentId])) {
                        $componentRequirements[$componentId] = [
                            'component_id' => $componentId,
                            'component_name' => $component->name,
                            'component_scu' => $component->scu,
                            // required_max: максимальное количество этого компонента, требуемое в ЛЮБОМ из выбранных заказов
                            // Это нужно для оптимизации: если компонент X используется в разных конфигурациях,
                            // мы берём максимум требуемого по всем заказам (не сумму!)
                            'required_max' => 0,
                            'in_stock' => $component->stock->available ?? 0,
                        ];
                    }
                    
                    // ФОРМУЛА 1: Максимум требуемого по всем заказам
                    // Если компонент нужен в заказе 1 (10 шт) и заказе 2 (5 шт),
                    // берём max(10, 5) = 10 шт, а не 10+5=15!
                    // Это потому что компонент можно переиспользовать между заказами в пределах максимума
                    $componentRequirements[$componentId]['required_max'] = 
                        max($componentRequirements[$componentId]['required_max'], $requiredForThisOrder);
                }
            }
            // Type 2: Компонент напрямую (не конфигурация)
            // Этот компонент сам является конечным изделием в заказе
            else {
                $orderQty = $order->quantity_ordered;
                $componentId = $product->id;
                
                // Type 2 НЕ добавляем в потребность (только его подкомпоненты Type 3)
                // Рекурсивно разбираем его на подкомпоненты Type 3
                foreach ($product->componentsInConfiguration as $subComponent) {
                    $requiredPerComponent = $subComponent->pivot->quantity;
                    $requiredForThisOrder = $orderQty * $requiredPerComponent;
                    
                    $subComponentId = $subComponent->id;
                    
                    if (!isset($componentRequirements[$subComponentId])) {
                        $componentRequirements[$subComponentId] = [
                            'component_id' => $subComponentId,
                            'component_name' => $subComponent->name,
                            'component_scu' => $subComponent->scu,
                            'required_max' => 0,
                            'in_stock' => $subComponent->stock->available ?? 0,
                        ];
                    }
                    
                    // Максимум требуемого подкомпонента
                    $componentRequirements[$subComponentId]['required_max'] = 
                        max($componentRequirements[$subComponentId]['required_max'], $requiredForThisOrder);
                }
            }
        }

        // Рассчитать потребности для каждого компонента
        $components = [];
        foreach ($componentRequirements as $comp) {
            $inStock = $comp['in_stock'];
            $required = $comp['required_max'];
            
            // ФОРМУЛА 2: Нужно произвести = Требуется - На складе
            // Пример: нужно 10 шт, на складе 2 шт → нужно произвести 10-2 = 8 шт
            // max(0, ...) гарантирует, что не будет отрицательных значений
            // (если на складе больше, чем требуется, то произвести не нужно)
            $needProduce = max(0, $required - $inStock);
            
            $components[] = [
                'component_id' => $comp['component_id'],
                'component_name' => $comp['component_name'],
                'component_scu' => $comp['component_scu'],
                'required_total' => $required,
                'in_stock' => $inStock,
                'need_to_produce' => $needProduce,
            ];
            
            $totalRequired += $required;
            $totalInStock += $inStock;
            $totalNeedProduce += $needProduce;
        }

        // ФОРМУЛА 3: Можно собрать = минимум из всех компонентов
        // Это виртуальный остаток: сколько ПОЛНЫХ комплектов шкафов можно собрать прямо сейчас
        // из того, что есть на складе, без производства новых компонентов
        // Пример: Место 1 на складе 5 шт (для 5 шкафов)
        //          Место 2 на складе 5 шт (для 5 шкафов)
        //          Место 3 на складе 2 шт (для 2 шкафов) ← ЛИМИТ!
        // Можно собрать = min(5, 5, 2) = 2 полных комплекта шкафов
        $canAssemble = count($components) > 0 ? PHP_INT_MAX : 0;
        foreach ($components as $comp) {
            // Для каждого компонента рассчитываем, сколько полных комплектов из него можно собрать
            // intval() округляет вниз (целые комплекты)
            // max(1, ...) защищает от деления на ноль
            $canAssembleFromThisComponent = intval($comp['in_stock'] / max(1, $comp['required_total']));
            // Берём минимум - это будет узким местом для сборки
            $canAssemble = min($canAssemble, $canAssembleFromThisComponent);
        }
        if ($canAssemble === PHP_INT_MAX) $canAssemble = 0;

        return response()->json([
            'selected_count' => count($selectedOrders),
            'total_required' => $totalRequired,
            'can_assemble' => $canAssemble,
            'need_to_produce' => $totalNeedProduce,
            'total_in_stock' => $totalInStock,
            'components' => $components
        ]);
    }

    /**
     * Утвердить план и создать производственный заказ
     * 
     * Алгоритм:
     * 1. Получить результаты анализа потребности (компоненты, которые нужно произвести)
     * 2. Создать новый заказ в таблице orders
     * 3. Для КАЖДОГО компонента из результата создать новую запись в production_orders
     * 4. Обновить статус СТАРОГО заказа на "In Production"
     */
    public function approvePlan(Request $request)
    {
        $orderIds = $request->input('order_ids', []);
        $components = $request->input('components', []);
        
        if (empty($orderIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Не выбраны заказы'
            ], 400);
        }

        if (empty($components)) {
            return response()->json([
                'success' => false,
                'message' => 'Нет данных о потребности в компонентах'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Шаг 1: Получить выбранные production_orders (для определения исходного заказа)
            $selectedOrders = ProductionOrder::whereIn('id', $orderIds)
                ->with('order', 'product')
                ->get();

            if ($selectedOrders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Производственные заказы не найдены'
                ], 404);
            }

            // Получить старый заказ (по которому был сделан расчет плана)
            $oldOrder = $selectedOrders->first()->order;
            if (!$oldOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Исходный заказ не найден'
                ], 404);
            }

            // Определить тип заказа на основе КОМПОНЕНТОВ которые будут производиться
            // Смотрим на типы компонентов в $components, а не на типы исходных заказов!
            $componentProductTypes = collect($components)
                ->map(fn($comp) => Detail::find($comp['component_id'])?->product_type_id)
                ->filter()
                ->unique();
            
            // ТОЛЬКО Type 1 компоненты → VO, иначе → PO
            $isVirtualOrder = $componentProductTypes->count() === 1 && $componentProductTypes->first() === 1;
            $orderPrefix = $isVirtualOrder ? 'VO' : 'PO';

            // Шаг 2: Получить следующий номер для нового заказа
            $lastOrder = Order::where('order_num', 'like', $orderPrefix . '%')
                ->latest('order_num')
                ->first();
            
            $lastNum = 0;
            if ($lastOrder) {
                $lastNum = intval(str_replace($orderPrefix . '-', '', $lastOrder->order_num));
            }
            $nextOrderNum = $orderPrefix . '-' . str_pad($lastNum + 1, 5, '0', STR_PAD_LEFT);

            // Шаг 3: Создать новый заказ
            $newOrder = Order::create([
                'order_num' => $nextOrderNum,
                'order_date' => now(),
                'planned_date' => now(),
                'planned_date' => now()->addDays(7),
                'status' => 'Production',
            ]);

            // Шаг 4: Получить статус "В производстве"
            $productionStatus = ProductionOrderStatus::where('name', 'like', '%В производстве%')
                ->orWhere('name', 'like', '%Production%')
                ->first();
            
            if (!$productionStatus) {
                $productionStatus = ProductionOrderStatus::where('name', 'В производстве')->first();
            }
            
            if (!$productionStatus) {
                // Не создавать новый! Использовать существующий
                $productionStatus = ProductionOrderStatus::first();
            }

            // Шаг 5: Создать production_orders для КАЖДОГО компонента, который нужно произвести
            $newProductionOrdersCount = 0;
            foreach ($components as $comp) {
                // Только если нужно произвести (need_to_produce > 0)
                if (isset($comp['need_to_produce']) && $comp['need_to_produce'] > 0) {
                    ProductionOrder::create([
                        'order_id' => $newOrder->id,
                        'reference_order' => $oldOrder->order_num,  // ← reference_order = исходный заказ, не новый!
                        'product_id' => $comp['component_id'],
                        'quantity_ordered' => $comp['need_to_produce'],
                        'quantity_received' => 0,
                        'status_id' => $productionStatus->id,
                        'planned_date' => $newOrder->planned_date,
                        'production_num' => null,
                    ]);
                    $newProductionOrdersCount++;
                }
            }

            // Если нет компонентов для производства, вернуть ошибку
            if ($newProductionOrdersCount === 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Все необходимые компоненты уже есть на складе. Нечего производить.'
                ], 400);
            }

            // Шаг 6: Обновить статус production_orders СТАРОГО заказа на "В производстве"
            // Получить статус "В производстве"
            $inProductionStatus = ProductionOrderStatus::where('name', 'В производстве')->first();
            
            if ($inProductionStatus) {
                // Обновить status_id у всех production_orders связанных со СТАРЫМ заказом (по order_id)
                ProductionOrder::where('order_id', $oldOrder->id)
                    ->update(['status_id' => $inProductionStatus->id]);
            }
            
            // Также обновить статус в самой таблице orders
            $oldOrder->update([
                'status' => 'В производстве'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "План утвержден. Создан заказ № $nextOrderNum с $newProductionOrdersCount производственными позициями",
                'order_id' => $newOrder->id,
                'order_num' => $nextOrderNum,
                'positions_count' => $newProductionOrdersCount,
                'redirect' => route('production-orders.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании заказа: ' . $e->getMessage()
            ], 500);
        }
    }
}
