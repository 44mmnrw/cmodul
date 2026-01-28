<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Detail;
use App\Models\DesiredStockLevel;
use App\Services\ForecastCalculationService;

echo "=== Тест системы прогнозирования остатков ===\n\n";

// 1. Получаем Type 1 конфигурацию
echo "[1] Получение Type 1 конфигураций:\n";
$configs = Detail::where('product_type_id', 1)->with('componentsInConfiguration.stock')->get();

if ($configs->isEmpty()) {
    echo "    [!] Type 1 конфигураций не найдено\n";
    exit(1);
}

echo "    [✓] Найдено конфигураций Type 1: " . $configs->count() . "\n";

// 2. Устанавливаем целевые остатки
echo "\n[2] Установка целевых остатков:\n";

foreach ($configs->take(3) as $config) {
    $existing = DesiredStockLevel::where('product_id', $config->id)->first();
    
    if (!$existing) {
        $desiredLevel = DesiredStockLevel::create([
            'product_id' => $config->id,
            'desired_quantity' => rand(30, 100), // Случайный целевой остаток
        ]);
        echo "    [✓] Установлен целевой остаток для '$config->name': {$desiredLevel->desired_quantity}\n";
    } else {
        echo "    [!] Целевой остаток для '$config->name' уже установлен: {$existing->desired_quantity}\n";
    }
}

// 3. Рассчитываем потребности
echo "\n[3] Расчет потребности в компонентах:\n";

$forecastService = new ForecastCalculationService();
$allForecasts = $forecastService->calculateAllComponentRequirements();

if (empty($allForecasts)) {
    echo "    [!] Нет конфигураций с установленными целевыми остатками\n";
    exit(0);
}

echo "    [✓] Рассчитано прогнозов: " . count($allForecasts) . "\n\n";

foreach ($allForecasts as $forecast) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📦 Конфигурация: {$forecast['configuration_name']}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "  Целевой остаток:          {$forecast['desired_quantity']} шт\n";
    echo "  Текущий виртуальный:      {$forecast['current_virtual_stock']} шт\n";
    echo "  Дефицит:                  {$forecast['shortage']} шт\n";
    echo "  Всего компонентов к заказу: {$forecast['total_components_to_order']} шт\n";
    echo "\n  Потребности в компонентах:\n";

    foreach ($forecast['components_needed'] as $needed) {
        $bottleneck = $needed['is_bottleneck'] ? ' 🚨 ЛИМИТ' : '';
        echo sprintf(
            "    • %-30s нужно: %3d | есть: %3d | заказать: %3d%s\n",
            substr($needed['component_name'], 0, 30),
            $needed['needed_for_all_configs'],
            $needed['current_stock'],
            $needed['recommend_to_order'],
            $bottleneck
        );
    }
    
    echo "\n";
}

// 4. Общая сводка по заказам
echo "\n[4] Сводка по всем необходимым заказам:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$orderSummary = $forecastService->getOrderSummary();

if (empty($orderSummary)) {
    echo "  [!] Нет необходимости в заказах\n";
} else {
    foreach ($orderSummary as $item) {
        if ($item['total_recommend_to_order'] > 0) {
            echo "  📋 {$item['component_name']}\n";
            echo "     Всего к заказу: {$item['total_recommend_to_order']} шт\n";
            echo "     Используется в конфигурациях:\n";
            
            foreach ($item['used_by_configurations'] as $usage) {
                echo "       - {$usage['configuration_name']}: {$usage['required_quantity']} шт\n";
            }
            echo "\n";
        }
    }
}

echo "\n[✓] Тест завершен успешно!\n";
