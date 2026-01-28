<?php

require 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Detail;

// Получить первую конфигурацию Type 1
$config = Detail::where('product_type_id', 1)->first();

if ($config) {
    echo "✅ Конфигурация: {$config->name} (id={$config->id})\n";
    
    $components = $config->componentsInConfiguration()->get();
    echo "📦 Компонентов: " . $components->count() . "\n";
    
    if ($components->isNotEmpty()) {
        foreach ($components as $comp) {
            echo "  - {$comp->name} (Type={$comp->product_type_id}): требуется {$comp->pivot->quantity} шт\n";
        }
    }
    
    // Попробовать рассчитать виртуальный остаток
    echo "\n🔍 Расчет виртуального остатка:\n";
    $virtualStock = $config->getVirtualStock();
    echo "  Виртуальный остаток: {$virtualStock['quantity']} шт\n";
    if ($virtualStock['limiting_component']) {
        echo "  Лимитирующий компонент: {$virtualStock['limiting_component']->name}\n";
    }
} else {
    echo "❌ Конфигурации Type 1 не найдены\n";
}
