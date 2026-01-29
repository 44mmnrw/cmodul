<?php
/**
 * Дебаг расчета компонентов потребности
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductionOrder;
use App\Models\ProductionOrderStatus;

echo "=== Дебаг расчета компонентов потребности ===\n\n";

// Получить первые 2 утвержденных заказа
$approvedStatus = ProductionOrderStatus::where('name', 'like', '%утвержд%')
    ->orWhere('name', 'like', '%approved%')
    ->first();

$orders = ProductionOrder::where('status_id', $approvedStatus->id)
    ->with('product.stock', 'product.componentsInConfiguration.stock')
    ->limit(2)
    ->get();

echo "Выбранные заказы: " . count($orders) . "\n\n";

$orderIds = $orders->pluck('id')->toArray();

// Имитация контроллера
$selectedOrders = ProductionOrder::whereIn('id', $orderIds)
    ->with('product.stock', 'product.componentsInConfiguration.stock', 'orderStatus')
    ->get();

$componentRequirements = [];

echo "=== Логика расчета ===\n\n";

foreach ($selectedOrders as $idx => $order) {
    echo "Заказ #" . ($idx + 1) . ":\n";
    echo "  ID: {$order->id}\n";
    echo "  Num: {$order->production_num}\n";
    echo "  Product ID: {$order->product_id}\n";
    echo "  Product Name: {$order->product->name}\n";
    echo "  Product Type: {$order->product->product_type_id}\n";
    echo "  Qty Ordered: {$order->quantity_ordered}\n\n";
    
    $product = $order->product;
    
    if ($product->product_type_id == 1) {
        echo "  ➤ Type 1 (Конфигурация) - разбираем на компоненты:\n";
        $orderQty = $order->quantity_ordered;
        
        foreach ($product->componentsInConfiguration as $component) {
            $requiredPerConfig = $component->pivot->quantity;
            $requiredForOrder = $orderQty * $requiredPerConfig;
            
            echo "    - {$component->name}\n";
            echo "      Required per config: {$requiredPerConfig}\n";
            echo "      Order qty: {$orderQty}\n";
            echo "      Required for order: {$requiredForOrder} ({$orderQty} × {$requiredPerConfig})\n";
            echo "      In stock: {$component->stock->available}\n\n";
            
            $componentId = $component->id;
            if (!isset($componentRequirements[$componentId])) {
                $componentRequirements[$componentId] = [
                    'component_id' => $componentId,
                    'component_name' => $component->name,
                    'component_scu' => $component->scu,
                    'required_total' => 0,
                    'in_stock' => $component->stock->available ?? 0,
                ];
            }
            $componentRequirements[$componentId]['required_total'] += $requiredForOrder;
        }
    } else {
        echo "  ➤ Type 2 (Компонент) - берем как есть:\n";
        $orderQty = $order->quantity_ordered;
        echo "    - {$product->name}\n";
        echo "      Required: {$orderQty}\n";
        echo "      In stock: {$product->stock->available}\n\n";
        
        $componentId = $product->id;
        if (!isset($componentRequirements[$componentId])) {
            $componentRequirements[$componentId] = [
                'component_id' => $componentId,
                'component_name' => $product->name,
                'component_scu' => $product->scu,
                'required_total' => 0,
                'in_stock' => $product->stock->available ?? 0,
            ];
        }
        $componentRequirements[$componentId]['required_total'] += $orderQty;
    }
}

echo "\n=== Итоговые требования ===\n\n";

foreach ($componentRequirements as $comp) {
    $inStock = $comp['in_stock'];
    $required = $comp['required_total'];
    $needProduce = max(0, $required - $inStock);
    
    echo "{$comp['component_name']} ({$comp['component_scu']})\n";
    echo "  Требуется: {$required} шт\n";
    echo "  На складе: {$inStock} шт\n";
    echo "  Нужно произвести: {$needProduce} шт\n\n";
}

echo "=== Завершено ===\n";
