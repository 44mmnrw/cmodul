<?php

use Illuminate\Support\Facades\Artisan;

require __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Detail;
use App\Models\Stock;
use DB;

echo "🔍 Проверка данных для виртуальных остатков\n";
echo "=" . str_repeat("=", 60) . "\n\n";

// 1. Получить первую конфигурацию
$config = Detail::where('product_type_id', 1)->first();

if (!$config) {
    echo "❌ Конфигурации Type 1 не найдены!\n";
    exit(1);
}

echo "✅ Конфигурация найдена: {$config->name} (id={$config->id})\n\n";

// 2. Проверить компоненты
$components = $config->componentsInConfiguration()->get();
echo "📦 Компоненты конфигурации: " . $components->count() . "\n";

if ($components->isEmpty()) {
    echo "⚠️ Нет компонентов у этой конфигурации!\n";
    echo "   Нужно добавить связи в таблицу configs\n\n";
} else {
    echo "   Список компонентов:\n";
    foreach ($components as $comp) {
        $stock = $comp->stock;
        echo "   - {$comp->name} (id={$comp->id})\n";
        if ($stock) {
            echo "     Остатки: {$stock->quantity} шт (доступно: {$stock->available})\n";
        } else {
            echo "     ⚠️ Остатки не найдены в таблице stocks\n";
        }
    }
}

echo "\n";

// 3. Попытаться рассчитать виртуальный остаток
echo "🔢 Расчет виртуального остатка:\n";
$virtualStock = $config->getVirtualStock();

if ($virtualStock) {
    echo "   Виртуальный остаток: {$virtualStock['quantity']} шт\n";
    if ($virtualStock['limiting_component']) {
        echo "   Лимитирующий компонент: {$virtualStock['limiting_component']->name}\n";
    }
} else {
    echo "   ❌ Ошибка при расчете\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "✅ Проверка завершена\n";
