<?php
/**
 * Скрипт для создания тестовых ProductionOrder
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductionOrder;
use App\Models\ProductionOrderStatus;
use App\Models\Detail;

echo "=== Создание тестовых заказов ===\n\n";

// Найти статус "Утверждено"
$approvedStatus = ProductionOrderStatus::where('name', 'like', '%утвержд%')
    ->orWhere('name', 'like', '%approved%')
    ->first();

if (!$approvedStatus) {
    // Создать статус если его нет
    $approvedStatus = ProductionOrderStatus::create([
        'name' => 'Утверждено',
        'color' => '#10b981',
        'description' => 'Заказ утвержден и готов к производству',
        'sort_order' => 5
    ]);
    echo "✓ Создан новый статус: {$approvedStatus->name}\n\n";
} else {
    echo "✓ Найден статус: {$approvedStatus->name} (ID: {$approvedStatus->id})\n\n";
}

// Найти Type 1 конфигурации для создания заказов
$configurations = Detail::where('product_type_id', 1)
    ->with('stock', 'componentsInConfiguration.stock')
    ->limit(5)
    ->get();

echo "Найдено Type 1 конфигураций: " . count($configurations) . "\n\n";

if (count($configurations) > 0) {
    // Удалить старые тестовые заказы если есть
    ProductionOrder::where('production_num', 'like', 'TEST-%')->delete();
    echo "Очищены старые тестовые заказы\n\n";
    
    // Создать тестовые заказы
    $created = 0;
    foreach ($configurations as $config) {
        $testOrder = ProductionOrder::create([
            'production_num' => 'TEST-' . str_pad($created + 1, 4, '0', STR_PAD_LEFT),
            'product_id' => $config->id,
            'quantity_ordered' => rand(2, 8),
            'quantity_received' => 0,
            'status_id' => $approvedStatus->id,
            'planned_date' => now()->addDays(rand(3, 10)),
            'notes' => 'Тестовый заказ для проверки планирования (конфигурация: ' . $config->scu . ')'
        ]);
        
        $created++;
        echo "✓ Создан заказ #{$created}: {$testOrder->production_num}\n";
        echo "  Product: {$config->name} (Type 1)\n";
        echo "  Qty: {$testOrder->quantity_ordered}, Status: {$approvedStatus->name}\n\n";
    }
    
    echo "Всего создано заказов: {$created}\n";
} else {
    echo "✗ Type 1 конфигурации не найдены!\n";
}

// Проверка: вывести все утвержденные заказы
echo "\n=== Все утвержденные заказы ===\n";
$allApprovedOrders = ProductionOrder::where('status_id', $approvedStatus->id)
    ->with('product.stock', 'product.componentsInConfiguration.stock', 'orderStatus')
    ->get();

echo "Всего утвержденных заказов: " . count($allApprovedOrders) . "\n\n";

foreach ($allApprovedOrders as $order) {
    echo "ID: {$order->id}\n";
    echo "  Номер: {$order->production_num}\n";
    echo "  Product: {$order->product->name} (Type {$order->product->product_type_id})\n";
    echo "  Qty: {$order->quantity_ordered}\n";
    
    if ($order->product->product_type_id == 1) {
        $virtualStock = $order->product->getVirtualStock();
        echo "  Virtual Stock: {$virtualStock['quantity']}\n";
        if (isset($virtualStock['limiting_component'])) {
            echo "  Limiting: {$virtualStock['limiting_component']->name}\n";
        }
    }
    
    echo "\n";
}

echo "=== Завершено ===\n";
