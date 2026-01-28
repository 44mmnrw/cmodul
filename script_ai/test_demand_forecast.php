<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Detail;
use App\Models\Stock;
use App\Services\DemandForecastService;

$service = new DemandForecastService();

echo "========================================\n";
echo "  Демонстрация расчета потребности\n";
echo "========================================\n\n";

// Получим все Type 2 компоненты
$components = Detail::where('product_type_id', 2)->get();

if ($components->isEmpty()) {
    echo "[!] Компонентов Type 2 не найдено\n";
    echo "[*] Создаем тестовые данные...\n";
    
    // Создадим тестовый компонент
    $component = Detail::create([
        'name' => 'Компонент тестовый',
        'product_type_id' => 2,
        'min_quantity' => 50, // неснижаемый остаток
    ]);
    
    // Создадим остаток
    Stock::create([
        'product_id' => $component->id,
        'quantity' => 30, // сейчас 30 шт
    ]);
    
    echo "[✓] Создан компонент ID=" . $component->id . "\n";
    $components = collect([$component]);
}

echo "[*] Анализируем " . $components->count() . " компонентов Type 2\n\n";

// Рассчитаем потребность для каждого компонента
foreach ($components as $component) {
    $demand = $service->calculateComponentDemand($component);
    
    if (isset($demand['error'])) {
        echo "[!] " . $demand['error'] . "\n";
        continue;
    }
    
    echo "═══════════════════════════════════════════\n";
    echo "[📦] Компонент: {$demand['component_name']} (ID: {$demand['component_id']})\n";
    echo "═══════════════════════════════════════════\n";
    echo "[📊] Неснижаемый остаток (min): {$demand['min_quantity']} шт\n";
    echo "[📈] Текущий остаток на складе: {$demand['current_stock']} шт\n";
    echo "[🏭] В производстве (ожидаем): {$demand['quantity_in_production']} шт\n";
    echo "[✅] Всего доступно: {$demand['total_available']} шт\n";
    echo "[📉] Дефицит: {$demand['demand']} шт\n";
    
    if ($demand['need_to_order'] > 0) {
        echo "[🔴] ⚠️  ТРЕБУЕТСЯ ЗАКАЗ: {$demand['need_to_order']} шт\n";
    } else {
        echo "[🟢] ✓ Запас достаточен\n";
    }
    
    // Показать активные заказы в производстве
    if (!empty($demand['production_orders'])) {
        echo "\n[📋] Активные заказы в производстве:\n";
        foreach ($demand['production_orders'] as $idx => $po) {
            echo "    [{idx}] Заказано {$po['quantity_ordered']} шт, получено {$po['quantity_received']} шт, ожидаем {$po['quantity_pending']} шт (статус: {$po['status']})\n";
        }
    } else {
        echo "\n[📋] Активных заказов нет\n";
    }
    
    echo "\n";
}

// Создадим тестовый заказ в производство
echo "\n" . str_repeat("=", 45) . "\n";
echo "  Тестирование создания заказа\n";
echo str_repeat("=", 45) . "\n\n";

if ($components->isNotEmpty()) {
    $component = $components->first();
    
    $productionOrder = $service->createProductionOrder(
        $component,
        100,
        'Тестовый заказ'
    );
    
    echo "[✓] Создан заказ в производство:\n";
    echo "    - Компонент: {$component->name} (ID: {$component->id})\n";
    echo "    - Количество: 100 шт\n";
    echo "    - Статус: {$productionOrder->status}\n";
    echo "    - ID заказа: {$productionOrder->id}\n";
    
    // Пересчитаем потребность с учетом нового заказа
    echo "\n[*] Пересчет потребности с учетом заказа...\n";
    $newDemand = $service->calculateComponentDemand($component);
    
    echo "[📊] Новые показатели:\n";
    echo "    - В производстве: {$newDemand['quantity_in_production']} шт\n";
    echo "    - Требуется заказать: {$newDemand['need_to_order']} шт\n";
}

// Вывести все активные заказы в производстве
echo "\n" . str_repeat("=", 45) . "\n";
echo "  Все активные заказы в производстве\n";
echo str_repeat("=", 45) . "\n\n";

$activeOrders = $service->getActiveOrders();

if ($activeOrders->isEmpty()) {
    echo "[!] Активных заказов нет\n";
} else {
    echo "[*] Всего активных заказов: {$activeOrders->count()}\n\n";
    
    foreach ($activeOrders as $order) {
        echo "[📦] {$order->product->name}\n";
        echo "    - Заказано: {$order->quantity_ordered} шт\n";
        echo "    - Получено: {$order->quantity_received} шт\n";
        echo "    - Ожидаем: {$order->getPendingQuantity()} шт\n";
        echo "    - Статус: {$order->status}\n\n";
    }
}

echo "\n[✓] Демонстрация завершена\n";
