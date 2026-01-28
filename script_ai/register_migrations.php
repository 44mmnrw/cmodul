<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 РЕГИСТРАЦИЯ МИГРАЦИЙ КАК ВЫПОЛНЕННЫХ\n";
echo str_repeat("=", 70) . "\n\n";

$batch = DB::table('migrations')->max('batch') ?? 0;
$nextBatch = $batch + 1;

$migrations = [
    '2026_01_27_120100_create_stock_movements_table',
    '2026_01_27_140000_add_document_number_to_stock_movements',
    '2026_01_28_100000_add_client_fields_to_stock_movements'
];

foreach ($migrations as $migration) {
    $exists = DB::table('migrations')->where('migration', $migration)->exists();
    
    if (!$exists) {
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => $nextBatch
        ]);
        echo "✅ {$migration}\n";
    } else {
        echo "⚠️  {$migration} (уже зарегистрирована)\n";
    }
}

echo "\n✅ ВСЕ МИГРАЦИИ ЗАРЕГИСТРИРОВАНЫ\n";
