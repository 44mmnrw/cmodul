<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Detail;
use App\Models\DesiredStockLevel;
use App\Models\DemandForecast;
use App\Models\ProcurementRecommendation;
use App\Services\ForecastCalculationService;

echo "=== ТЕСТИРОВАНИЕ СИСТЕМЫ ПРОГНОЗА ЗАКАЗОВ ===\n\n";

// 1️⃣ Проверяем существующие данные
echo "📊 1. ТЕКУЩИЕ ДАННЫЕ\n";
echo str_repeat("-", 60) . "\n";

$type1Products = Detail::where('product_type_id', 1)->get();
echo "Type 1 (конфигурации): {$type1Products->count()} шт\n";

foreach ($type1Products->take(3) as $product) {
    $virtualStock = $product->getVirtualStock();
    if (!$virtualStock) {
        echo "  • {$product->name}: нет данных о компонентах\n";
        continue;
    }
    echo "  • {$product->name}: виртуальный остаток = {$virtualStock['quantity']} шт";
    if (isset($virtualStock['limiting_component']) && $virtualStock['limiting_component']) {
        echo " (лимит: {$virtualStock['limiting_component']->name})";
    }
    echo "\n";
}

// 2️⃣ Создаём желаемые остатки для первых 3 Type 1
echo "\n✅ 2. УСТАНОВКА ЖЕЛАЕМЫХ ОСТАТКОВ\n";
echo str_repeat("-", 60) . "\n";

foreach ($type1Products->take(3) as $product) {
    $desired = DesiredStockLevel::updateOrCreate(
        ['product_id' => $product->id],
        ['desired_quantity' => 15]  // Хотим иметь 15 шт
    );
    echo "  • {$product->name}: желаемый остаток = 15 шт\n";
}

// 3️⃣ Запускаем расчёт прогнозов
echo "\n🔄 3. РАСЧЁТ ПРОГНОЗОВ\n";
echo str_repeat("-", 60) . "\n";

$service = new ForecastCalculationService();
$recommendations = $service->calculateAllForecasts();

echo "Создано прогнозов: " . DemandForecast::count() . " шт\n";

// 4️⃣ Выводим результаты прогнозов по Type 1
echo "\n📈 4. РЕЗУЛЬТАТЫ ПРОГНОЗОВ (Type 1)\n";
echo str_repeat("-", 60) . "\n";

$type1Forecasts = DemandForecast::where('product_type_id', 1)
    ->with('product', 'bottleneckProduct')
    ->get();

foreach ($type1Forecasts as $forecast) {
    $status_icon = match($forecast->forecast_status) {
        'OK' => '✅',
        'WARNING' => '⚠️',
        'CRITICAL' => '🔴',
        default => '❓'
    };
    
    echo "{$status_icon} {$forecast->product->name}\n";
    echo "   Текущий: {$forecast->current_stock_calculated} шт | Желаемый: {$forecast->desired_stock} шт | Дефицит: {$forecast->gap} шт\n";
    if ($forecast->bottleneckProduct) {
        echo "   Лимитирующий компонент: {$forecast->bottleneckProduct->name}\n";
    }
    echo "\n";
}

// 5️⃣ Выводим требуемые Type 2
echo "\n📦 5. ТРЕБУЕМЫЕ КОМПОНЕНТЫ (Type 2)\n";
echo str_repeat("-", 60) . "\n";

$type2Forecasts = DemandForecast::where('product_type_id', 2)
    ->where('gap', '>', 0)
    ->with('product', 'requiredFor')
    ->get();

foreach ($type2Forecasts as $forecast) {
    echo "• {$forecast->product->name}\n";
    echo "   Требуется: {$forecast->desired_stock} шт | Текущий остаток: {$forecast->current_stock_calculated} шт | Дефицит: {$forecast->gap} шт\n";
    echo "   Для: {$forecast->requiredFor?->name}\n\n";
}

// 6️⃣ Выводим требуемые Type 3
echo "\n🔩 6. ТРЕБУЕМЫЕ МАТЕРИАЛЫ (Type 3)\n";
echo str_repeat("-", 60) . "\n";

$type3Forecasts = DemandForecast::where('product_type_id', 3)
    ->where('gap', '>', 0)
    ->with('product', 'requiredFor')
    ->get();

if ($type3Forecasts->isNotEmpty()) {
    foreach ($type3Forecasts as $forecast) {
        echo "• {$forecast->product->name}\n";
        echo "   Требуется: {$forecast->desired_stock} шт | Текущий остаток: {$forecast->current_stock_calculated} шт | Дефицит: {$forecast->gap} шт\n";
        echo "   Для: {$forecast->requiredFor?->name}\n\n";
    }
} else {
    echo "Дополнительные материалы не требуются\n";
}

// 7️⃣ Создаём рекомендации по заказам
echo "\n📋 7. СОЗДАНИЕ РЕКОМЕНДАЦИЙ ПО ЗАКАЗАМ\n";
echo str_repeat("-", 60) . "\n";

$service->createRecommendationsFromForecasts();

$recommendations = ProcurementRecommendation::where('status', 'DRAFT')
    ->with('product', 'requiredFor')
    ->get();

echo "Создано рекомендаций: {$recommendations->count()} шт\n\n";

foreach ($recommendations as $rec) {
    $priority_emoji = match($rec->priority) {
        'CRITICAL' => '🔴',
        'HIGH' => '🟠',
        'MEDIUM' => '🟡',
        'LOW' => '🟢',
        default => '⚪'
    };
    
    echo "{$priority_emoji} {$rec->product->name}\n";
    echo "   Заказать: {$rec->recommended_quantity} шт [{$rec->priority}]\n";
    if ($rec->requiredFor) {
        echo "   Для: {$rec->requiredFor->name}\n";
    }
    if ($rec->reason) {
        echo "   Причина: {$rec->reason}\n";
    }
    echo "\n";
}

echo "\n✅ ЗАВЕРШЕНО\n";
echo "Всего прогнозов: " . DemandForecast::count() . "\n";
echo "Всего рекомендаций: " . ProcurementRecommendation::count() . "\n";
