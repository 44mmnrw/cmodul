<?php

namespace App\Services;

use App\Models\Detail;
use App\Models\Stock;
use App\Models\ProductionOrder;
use Illuminate\Database\Eloquent\Collection;

class DemandForecastService
{
    /**
     * Рассчитать потребность компонентов на основе:
     * - min_quantity (неснижаемый остаток)
     * - Текущие остатки в stocks
     * - Количество в процессе производства
     * 
     * Потребность = min_quantity - current_stock - quantity_pending
     */
    public function calculateComponentDemand(Detail $component): array
    {
        // Компонент должен быть Type 2
        if ($component->product_type_id !== 2) {
            return [
                'error' => 'Компонент должен быть Type 2',
                'component_id' => $component->id,
            ];
        }

        // Получаем параметры компонента
        $minQuantity = $component->min_quantity ?? 0;
        
        // Текущий остаток
        $stock = $component->stock;
        $currentStock = $stock ? $stock->quantity : 0;
        
        // Количество в производстве (ожидаем приемки)
        $productionOrders = ProductionOrder::where('product_id', $component->id)
            ->whereIn('status', ['ordering', 'in_production', 'ready'])
            ->get();
        
        $quantityInProduction = $productionOrders->sum('quantity_pending');
        
        // Расчет потребности
        $demand = $minQuantity - $currentStock - $quantityInProduction;
        $needToOrder = max(0, $demand);
        
        return [
            'component_id' => $component->id,
            'component_name' => $component->name,
            'product_type_id' => 2,
            'min_quantity' => $minQuantity,
            'current_stock' => $currentStock,
            'quantity_in_production' => $quantityInProduction,
            'total_available' => $currentStock + $quantityInProduction,
            'demand' => $demand,
            'need_to_order' => $needToOrder,
            'production_orders' => $productionOrders->map(fn($po) => [
                'id' => $po->id,
                'quantity_ordered' => $po->quantity_ordered,
                'quantity_received' => $po->quantity_received,
                'quantity_pending' => $po->quantity_pending,
                'status' => $po->status,
            ]),
        ];
    }

    /**
     * Рассчитать потребность во всех компонентах Type 2
     */
    public function calculateAllDemand(): Collection
    {
        $components = Detail::where('product_type_id', 2)
            ->with('stock')
            ->get();
        
        return $components->map(function ($component) {
            return $this->calculateComponentDemand($component);
        });
    }

    /**
     * Рассчитать потребность в материалах Type 3 на основе заказов в производство
     * 
     * Логика:
     * - Берем всем активные ProductionOrder (компоненты Type 2)
     * - Для каждого берем конфигурации (configs), где он используется как slave
     * - Определяем, какие Type 3 материалы нужны для производства
     * - Считаем потребность = quantity_needed - quantity_already_used
     */
    public function calculateMaterialsForProduction(): array
    {
        $activeOrders = ProductionOrder::whereIn('status', ['ordering', 'in_production', 'ready'])
            ->get();
        
        $materialsNeeded = [];
        
        foreach ($activeOrders as $order) {
            $component = $order->product;
            
            // Найти конфигурации, которые используют этот компонент
            $configurations = $component->usedInCabinets()
                ->where('product_type_id', 1)
                ->get();
            
            foreach ($configurations as $config) {
                // Получить материалы (Type 3), которые нужны для этой конфигурации
                $materials = $config->componentsInConfiguration()
                    ->where('product_type_id', 3)
                    ->get();
                
                // Рассчитать, сколько материалов нужно для производства компонента
                foreach ($materials as $material) {
                    $requiredPerConfig = $material->pivot->quantity;
                    $requiredPerComponent = $requiredPerConfig; // примерно так
                    
                    $totalNeeded = $order->getPendingQuantity() * $requiredPerComponent;
                    
                    if (!isset($materialsNeeded[$material->id])) {
                        $materialsNeeded[$material->id] = [
                            'material_id' => $material->id,
                            'material_name' => $material->name,
                            'product_type_id' => 3,
                            'total_needed' => 0,
                            'orders' => [],
                        ];
                    }
                    
                    $materialsNeeded[$material->id]['total_needed'] += $totalNeeded;
                    $materialsNeeded[$material->id]['orders'][] = [
                        'production_order_id' => $order->id,
                        'component_id' => $component->id,
                        'component_name' => $component->name,
                        'quantity_needed' => $totalNeeded,
                    ];
                }
            }
        }
        
        return array_values($materialsNeeded);
    }

    /**
     * Создать заказ в производство для компонента
     */
    public function createProductionOrder(Detail $component, int $quantity, string $notes = ''): ProductionOrder
    {
        return ProductionOrder::create([
            'product_id' => $component->id,
            'quantity_ordered' => $quantity,
            'status' => 'ordering',
            'notes' => $notes,
        ]);
    }

    /**
     * Получить активные заказы в производство
     */
    public function getActiveOrders(): Collection
    {
        return ProductionOrder::whereIn('status', ['ordering', 'in_production', 'ready'])
            ->with('product')
            ->get();
    }
}
