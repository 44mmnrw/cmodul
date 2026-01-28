<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Содержимое таблицы migrations ===\n\n";

$migrations = DB::table('migrations')->get();

if ($migrations->isEmpty()) {
    echo "⚠️  Таблица миграций ПУСТА!\n";
} else {
    foreach ($migrations as $m) {
        echo "Migration: {$m->migration}\n";
        echo "  Batch: {$m->batch}\n\n";
    }
}

echo "=== Статус таблицы миграций ===\n";
echo "Всего записей: " . $migrations->count() . "\n";
echo "Max batch: " . ($migrations->max('batch') ?? '0') . "\n";
