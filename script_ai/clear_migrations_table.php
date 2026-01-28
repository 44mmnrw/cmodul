<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Очистка таблицы migrations ===\n\n";

// Удаляем все записи кроме системных (0001_)
$deleted = DB::table('migrations')
    ->where('migration', 'not like', '0001_%')
    ->delete();

echo "✅ Удалено записей: {$deleted}\n\n";

echo "=== Содержимое таблицы migrations после очистки ===\n";
$remaining = DB::table('migrations')->get();

if ($remaining->isEmpty()) {
    echo "Таблица полностью пуста\n";
} else {
    foreach ($remaining as $m) {
        echo "✓ {$m->migration} (batch: {$m->batch})\n";
    }
}

echo "\nВсего записей: " . $remaining->count() . "\n";
