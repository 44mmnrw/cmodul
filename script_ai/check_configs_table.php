<?php
/**
 * Проверка таблицы configs и связи
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Detail;
use Illuminate\Support\Facades\DB;

echo "=== Проверка таблицы configs ===\n\n";

// Посмотреть структуру таблицы
echo "Структура таблицы configs:\n";
$columns = DB::select("DESCRIBE configs");
foreach ($columns as $col) {
    echo "  {$col->Field} ({$col->Type})\n";
}

echo "\n=== Примеры данных из configs ===\n\n";

$configs = DB::table('configs')->limit(5)->get();
echo "Первые 5 записей:\n";
foreach ($configs as $config) {
    echo "  master_id: {$config->master_id}, slave_id: {$config->slave_id}, quantity: {$config->quantity}\n";
}

echo "\n=== Проверка связи для конкретной конфигурации (ID 176) ===\n\n";

$detail = Detail::find(176);
if ($detail) {
    echo "Конфигурация: {$detail->name} (ID: {$detail->id}, Type: {$detail->product_type_id})\n\n";
    
    // Получить компоненты
    $components = DB::table('configs')
        ->where('master_id', 176)
        ->join('products', 'configs.slave_id', '=', 'products.id')
        ->select('configs.*', 'products.name as component_name', 'products.scu')
        ->get();
    
    echo "Компоненты в составе (из БД):\n";
    foreach ($components as $comp) {
        echo "  slave_id: {$comp->slave_id}, name: {$comp->component_name}, scu: {$comp->scu}, quantity: {$comp->quantity}\n";
    }
    
    echo "\n=== Проверка через Eloquent связь ===\n\n";
    $detail->load('componentsInConfiguration');
    echo "Компоненты через Eloquent:\n";
    foreach ($detail->componentsInConfiguration as $comp) {
        echo "  ID: {$comp->id}, name: {$comp->name}, scu: {$comp->scu}\n";
        echo "    pivot->quantity: {$comp->pivot->quantity}\n";
        echo "    pivot attributes: " . implode(', ', array_keys($comp->pivot->getAttributes())) . "\n";
    }
}

echo "\n=== Завершено ===\n";
