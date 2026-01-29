<?php
/**
 * Финальная проверка интеграции ProductionOrder на странице планирования
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductionOrder;
use App\Models\ProductionOrderStatus;
use App\Models\Detail;

echo "=== Финальная проверка интеграции ===\n\n";

// 1. Проверить статус
$approvedStatus = ProductionOrderStatus::where('name', 'like', '%утвержд%')
    ->orWhere('name', 'like', '%approved%')
    ->first();

echo "1. Статус 'Утверждено':\n";
echo "   ID: {$approvedStatus->id}\n";
echo "   Name: {$approvedStatus->name}\n";
echo "   Color: {$approvedStatus->color}\n\n";

// 2. Получить все утвержденные заказы как в контроллере
$orders = ProductionOrder::query();

if ($approvedStatus) {
    $orders->where('status_id', $approvedStatus->id);
}

$orders = $orders
    ->with([
        'product' => function ($query) {
            $query->with('stock', 'componentsInConfiguration.stock');
        },
        'orderStatus'
    ])
    ->orderBy('created_at', 'desc')
    ->get();

echo "2. Утвержденные заказы (как в контроллере):\n";
echo "   Всего: " . count($orders) . "\n\n";

// 3. Проверить структуру каждого заказа
echo "3. Структура данных каждого заказа:\n";
foreach ($orders->take(3) as $i => $order) {
    echo "   Заказ " . ($i + 1) . ":\n";
    echo "     - production_num: {$order->production_num}\n";
    echo "     - quantity_ordered: {$order->quantity_ordered}\n";
    echo "     - created_at: {$order->created_at->format('d.m.Y')}\n";
    echo "     - planned_date: " . ($order->planned_date ? $order->planned_date->format('d.m.Y') : 'NULL') . "\n";
    echo "     - product.name: {$order->product->name}\n";
    echo "     - product.scu: {$order->product->scu}\n";
    echo "     - product.product_type_id: {$order->product->product_type_id}\n";
    
    if ($order->product->product_type_id == 1) {
        $virtualStock = $order->product->getVirtualStock();
        echo "     - getVirtualStock()['quantity']: {$virtualStock['quantity']}\n";
        if (isset($virtualStock['limiting_component'])) {
            echo "     - limiting_component: {$virtualStock['limiting_component']->name}\n";
        }
    }
    
    if ($order->product->stock) {
        echo "     - product.stock.available: {$order->product->stock->available}\n";
    } else {
        echo "     - product.stock: NULL (нормально для Type 1)\n";
    }
    
    echo "     - componentsInConfiguration count: " . count($order->product->componentsInConfiguration) . "\n";
    echo "\n";
}

// 4. Тест analyzeRequirements
echo "4. Тест JSON API analyzeRequirements:\n";
$testOrderIds = $orders->take(2)->pluck('id')->toArray();
echo "   Выбранные заказы: " . implode(', ', $testOrderIds) . "\n\n";

// Имитация API запроса
$selectedOrders = ProductionOrder::whereIn('id', $testOrderIds)
    ->with('product.stock', 'product.componentsInConfiguration', 'orderStatus')
    ->get();

$totalRequired = 0;
$totalCanAssemble = 0;
$totalNeedProduce = 0;
$requirementsByConfiguration = [];

foreach ($selectedOrders as $order) {
    $orderQty = $order->quantity_ordered;
    $totalRequired += $orderQty;

    $product = $order->product;
    
    if (!$product) {
        continue;
    }

    if ($product->product_type_id == 1) {
        $virtualStock = $product->getVirtualStock();
        $canAssemble = min($virtualStock['quantity'] ?? 0, $orderQty);
        $needProduce = max(0, $orderQty - $canAssemble);

        $totalCanAssemble += $canAssemble;
        $totalNeedProduce += $needProduce;

        $limitingComponent = $virtualStock['limiting_component']->name ?? 'Unknown';
        
        $requirementsByConfiguration[] = [
            'order_id' => $order->id,
            'order_name' => $product->name,
            'order_code' => $product->scu ?? 'N/A',
            'quantity_required' => $orderQty,
            'can_assemble' => $canAssemble,
            'need_to_produce' => $needProduce,
            'limiting_component' => $limitingComponent
        ];
    } else {
        $availableQty = $product->stock->available ?? 0;
        $canAssemble = min($availableQty, $orderQty);
        $needProduce = max(0, $orderQty - $canAssemble);

        $totalCanAssemble += $canAssemble;
        $totalNeedProduce += $needProduce;

        $requirementsByConfiguration[] = [
            'order_id' => $order->id,
            'order_name' => $product->name,
            'order_code' => $product->scu ?? 'N/A',
            'quantity_required' => $orderQty,
            'can_assemble' => $canAssemble,
            'need_to_produce' => $needProduce,
        ];
    }
}

echo "   Результат анализа:\n";
echo "   - selected_count: " . count($selectedOrders) . "\n";
echo "   - total_required: {$totalRequired}\n";
echo "   - can_assemble: {$totalCanAssemble}\n";
echo "   - need_to_produce: {$totalNeedProduce}\n";
echo "   - configurations: " . count($requirementsByConfiguration) . "\n\n";

foreach ($requirementsByConfiguration as $req) {
    echo "     {$req['order_name']} ({$req['order_code']})\n";
    echo "       Required: {$req['quantity_required']}, Can assemble: {$req['can_assemble']}, Need: {$req['need_to_produce']}\n";
    if (isset($req['limiting_component'])) {
        echo "       Limiting: {$req['limiting_component']}\n";
    }
}

echo "\n=== Все проверки завершены ===\n";
echo "\nСтраница доступна по адресу: http://127.0.0.1:8000/production-planning\n";
