<?php
/**
 * Проверка ProductionOrder и создание тестовых данных
 */

// Подключить Laravel
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductionOrder;
use App\Models\ProductionOrderStatus;
use App\Models\Detail;

echo "=== Проверка ProductionOrder ===\n";

// Проверить статусы
$statuses = ProductionOrderStatus::all();
echo "Найдено статусов: " . count($statuses) . "\n";
foreach ($statuses as $status) {
    echo "  ID: {$status->id}, Name: {$status->name}\n";
}

// Поиск статуса "Утверждено"
$approvedStatus = ProductionOrderStatus::where('name', 'like', '%утвержд%')
    ->orWhere('name', 'like', '%approved%')
    ->first();

if ($approvedStatus) {
    echo "\n✓ Найден статус 'Утверждено': ID={$approvedStatus->id}, Name={$approvedStatus->name}\n";
} else {
    echo "\n✗ Статус 'Утверждено' не найден!\n";
}

// Проверить заказы
$totalOrders = ProductionOrder::count();
echo "\nВсего ProductionOrder в БД: {$totalOrders}\n";

// Проверить заказы со статусом "Утверждено"
if ($approvedStatus) {
    $approvedOrders = ProductionOrder::where('status_id', $approvedStatus->id)
        ->with('product', 'orderStatus')
        ->get();
    
    echo "Утвержденных заказов: " . count($approvedOrders) . "\n";
    
    if (count($approvedOrders) > 0) {
        echo "\nПримеры утвержденных заказов:\n";
        foreach ($approvedOrders->take(3) as $order) {
            echo "  ID: {$order->id}, Num: {$order->production_num}, Qty: {$order->quantity_ordered}";
            if ($order->product) {
                echo ", Product: {$order->product->name} (Type {$order->product->product_type_id})";
            }
            echo "\n";
        }
    } else {
        echo "\n⚠️  Нет утвержденных заказов!\n";
        echo "Попытаемся создать тестовый заказ...\n";
        
        // Найти компонент (Type 2) для тестирования
        $component = Detail::where('product_type_id', 2)->first();
        if ($component) {
            echo "Найден компонент для теста: {$component->name} (ID: {$component->id})\n";
            
            // Создать тестовый заказ
            $testOrder = ProductionOrder::create([
                'production_num' => 'TEST-' . date('YmdHis'),
                'product_id' => $component->id,
                'quantity_ordered' => 10,
                'quantity_received' => 0,
                'status_id' => $approvedStatus->id,
                'planned_date' => now()->addDays(5),
                'notes' => 'Тестовый заказ для проверки системы'
            ]);
            
            echo "✓ Создан тестовый заказ: ID={$testOrder->id}, Num={$testOrder->production_num}\n";
        } else {
            echo "✗ Компоненты (Type 2) не найдены в БД!\n";
        }
    }
}

// Проверить связь product->stock
echo "\n=== Проверка связи product->stock ===\n";
$orderWithProduct = ProductionOrder::with('product.stock')->first();
if ($orderWithProduct && $orderWithProduct->product) {
    echo "Заказ ID={$orderWithProduct->id}\n";
    echo "  Product: {$orderWithProduct->product->name}\n";
    if ($orderWithProduct->product->stock) {
        echo "  Stock available: {$orderWithProduct->product->stock->available}\n";
    } else {
        echo "  Stock: NULL (компонент не имеет записи в stocks)\n";
    }
}

echo "\n=== Завершено ===\n";
