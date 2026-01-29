<?php
// Инициализация Laravel
$basePath = __DIR__ . '/..';
$app = require $basePath . '/bootstrap/app.php';

// Получить контейнер
$container = $app;

// Выполнить boostrap
$kernel = $container->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Теперь используем DB
use Illuminate\Support\Facades\DB;

echo "=== ТАБЛИЦА production_orders (последние 15 записей) ===\n\n";

$results = DB::table('production_orders')
    ->select(
        'production_orders.id',
        'production_orders.order_id',
        'orders.order_num',
        'production_orders.product_id',
        'products.name',
        'products.product_type_id',
        'production_orders.quantity_ordered',
        'production_orders.reference_order'
    )
    ->leftJoin('orders', 'production_orders.order_id', '=', 'orders.id')
    ->leftJoin('products', 'production_orders.product_id', '=', 'products.id')
    ->orderByDesc('production_orders.id')
    ->limit(15)
    ->get();

echo "id\torder_id\torder_num\tproduct_id\ttype\tname\t\t\t\tquantity\treference_order\n";
echo str_repeat("=", 150) . "\n";

foreach ($results as $row) {
    $typeName = $row->product_type_id == 1 ? 'Type 1' : 'Type ' . $row->product_type_id;
    echo $row->id . "\t" . $row->order_id . "\t\t" . ($row->order_num ?? 'NULL') . "\t\t" . $row->product_id . "\t\t" . $typeName . "\t" . substr($row->name ?? 'NULL', 0, 30) . "\t" . $row->quantity_ordered . "\t\t" . ($row->reference_order ?? 'NULL') . "\n";
}

echo "\n=== ТАБЛИЦА orders (последние 10 записей) ===\n\n";

$orders = DB::table('orders')
    ->select('id', 'order_num', 'order_date', 'planned_date', 'status')
    ->orderByDesc('id')
    ->limit(10)
    ->get();

echo "id\torder_num\t\torder_date\t\tplanned_date\t\tstatus\n";
echo str_repeat("=", 120) . "\n";

foreach ($orders as $order) {
    echo $order->id . "\t" . $order->order_num . "\t\t" . ($order->order_date ?? 'NULL') . "\t\t" . ($order->planned_date ?? 'NULL') . "\t\t" . ($order->status ?? 'NULL') . "\n";
}
