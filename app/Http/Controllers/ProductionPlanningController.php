<?php

namespace App\Http\Controllers;

use App\Models\Detail;
use App\Models\ProductionOrder;
use App\Models\ProductionOrderStatus;
use App\Models\Stock;
use Illuminate\Http\Request;

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
                
                if (!isset($componentRequirements[$componentId])) {
                    $componentRequirements[$componentId] = [
                        'component_id' => $componentId,
                        'component_name' => $product->name,
                        'component_scu' => $product->scu,
                        'required_max' => 0,
                        'in_stock' => $product->stock->available ?? 0,
                    ];
                }
                
                // ФОРМУЛА 1 (для Type 2): Максимум требуемого
                // Для компонента берём максимум количества по всем заказам
                // (использование аналогично Type 1)
                $componentRequirements[$componentId]['required_max'] = 
                    max($componentRequirements[$componentId]['required_max'], $orderQty);
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
}
