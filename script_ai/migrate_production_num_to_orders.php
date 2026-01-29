<?php
/**
 * Перенос production_num в orders таблицу
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductionOrder;
use Illuminate\Support\Facades\DB;

echo "=== Перенос production_num в orders ===\n\n";

// Получить все уникальные production_num
$productionNums = ProductionOrder::distinct()->pluck('production_num')->filter()->toArray();
echo "Найдено уникальных номеров заказов: " . count($productionNums) . "\n";

if (count($productionNums) === 0) {
    echo "Нет production_num для переноса\n";
    exit;
}

// Создать orders и привязать production_orders
$created = 0;
foreach ($productionNums as $prodNum) {
    // Получить первый production_order с этим номером (для даты)
    $firstProdOrder = ProductionOrder::where('production_num', $prodNum)->first();
    
    if (!$firstProdOrder) continue;
    
    // Создать order
    $order = DB::table('orders')->insertGetId([
        'order_num' => $prodNum,
        'date' => $firstProdOrder->created_at->toDateString(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    // Обновить все production_orders с этим production_num
    ProductionOrder::where('production_num', $prodNum)
        ->update(['order_id' => $order]);
    
    $created++;
    echo "✓ Создан order ID={$order}, order_num={$prodNum}\n";
}

echo "\nВсего создано orders: {$created}\n";

// Проверка
echo "\n=== Проверка ===\n";
$orders = DB::table('orders')->get();
echo "Всего orders в БД: " . count($orders) . "\n\n";

foreach ($orders as $order) {
    $prodOrdersCount = ProductionOrder::where('order_id', $order->id)->count();
    echo "Order ID={$order->id}, order_num={$order->order_num}, date={$order->date}\n";
    echo "  → Привязано production_orders: {$prodOrdersCount}\n";
}

echo "\n=== Завершено ===\n";
