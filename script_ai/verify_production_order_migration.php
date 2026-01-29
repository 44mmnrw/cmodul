<?php

use App\Models\ProductionOrder;
use App\Models\Order;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Тест 1: Проверить связь ProductionOrder -> Order
echo "=== ТЕСТ 1: Связь ProductionOrder -> Order ===\n";
$po = ProductionOrder::first();
if ($po) {
    echo "✓ ProductionOrder найден (ID: {$po->id})\n";
    echo "  Order ID: " . ($po->order_id ?? 'NULL') . "\n";
    if ($po->order) {
        echo "  ✓ Order загружена: {$po->order->order_num}\n";
    } else {
        echo "  ✗ Order не загружена\n";
    }
} else {
    echo "✗ ProductionOrder не найден\n";
}

// Тест 2: Проверить Order -> ProductionOrders
echo "\n=== ТЕСТ 2: Order -> ProductionOrders ===\n";
$order = Order::first();
if ($order) {
    echo "✓ Order найдена (ID: {$order->id}, Номер: {$order->order_num})\n";
    $poCount = $order->productionOrders()->count();
    echo "  ProductionOrders: $poCount шт\n";
} else {
    echo "✗ Order не найдена\n";
}

// Тест 3: Проверить таблицу production_orders
echo "\n=== ТЕСТ 3: Проверить структуру таблицы production_orders ===\n";
$columns = \DB::select("DESCRIBE production_orders");
$hasOrderId = false;
$hasProductionNum = false;

foreach ($columns as $col) {
    if ($col->Field === 'order_id') {
        $hasOrderId = true;
        echo "✓ Столбец 'order_id' есть\n";
    }
    if ($col->Field === 'production_num') {
        $hasProductionNum = true;
        echo "✗ Столбец 'production_num' ещё есть (должен быть удален)\n";
    }
}

if (!$hasOrderId) {
    echo "✗ Столбец 'order_id' отсутствует\n";
}

// Тест 4: Проверить заполнение данных
echo "\n=== ТЕСТ 4: Данные в таблицах ===\n";
$poCount = ProductionOrder::count();
$orderCount = Order::count();
echo "ProductionOrder: $poCount\n";
echo "Order: $orderCount\n";

echo "\n✅ Все проверки завершены!\n";
