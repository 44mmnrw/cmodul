<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Проверка таблицы desired_stock_levels ===\n\n";

if (Schema::hasTable('desired_stock_levels')) {
    echo "[✓] Таблица desired_stock_levels создана!\n\n";
    
    // Показываем структуру таблицы
    $columns = DB::select("DESCRIBE desired_stock_levels");
    
    echo "[*] Структура таблицы:\n";
    foreach ($columns as $col) {
        echo sprintf("  %-20s %s %s\n", 
            $col->Field, 
            $col->Type, 
            ($col->Null === 'NO' ? '(NOT NULL)' : '(nullable)')
        );
    }
    
    echo "\n[✓] Количество записей: " . DB::table('desired_stock_levels')->count() . "\n";
    
} else {
    echo "[✗] Таблица desired_stock_levels НЕ найдена!\n";
}

echo "\n=== Все таблицы в БД ===\n";
$tables = DB::select("SHOW TABLES");
foreach ($tables as $table) {
    $tableName = current((array)$table);
    echo "  - $tableName\n";
}
