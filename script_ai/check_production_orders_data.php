<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap/app.php';

use App\Models\ProductionOrder;
use Illuminate\Support\Facades\DB;

echo "=== ПРОВЕРКА ТАБЛИЦЫ production_orders ===\n\n";

// Получить последние 5 production_orders
$orders = ProductionOrder::latest('id')
    ->take(5)
    ->with('product', 'order')
    ->get();

foreach ($orders as $order) {
    echo "ID: {$order->id}\n";
    echo "  order_id: {$order->order_id}\n";
    echo "  reference_order: {$order->reference_order}\n";
    echo "  product_id: {$order->product_id} (Type: {$order->product->product_type_id}) - {$order->product->name}\n";
    echo "  quantity_ordered: {$order->quantity_ordered}\n";
    echo "  planned_date: {$order->planned_date}\n";
    if ($order->order) {
        echo "  order->order_num: {$order->order->order_num}\n";
    }
    echo "\n";
}

// Проверить последние Orders
echo "\n=== ПОСЛЕДНИЕ ЗАКАЗЫ (orders) ===\n\n";
$lastOrders = DB::table('orders')->latest('id')->take(5)->get();
foreach ($lastOrders as $order) {
    echo "ID: {$order->id}\n";
    echo "  order_num: {$order->order_num}\n";
    echo "  order_date: {$order->order_date}\n";
    echo "  planned_date: {$order->planned_date}\n";
    echo "\n";
}
