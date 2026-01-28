<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "[*] Удаление столбца quantity_received из production_orders...\n";

try {
    if (Schema::hasColumn('production_orders', 'quantity_received')) {
        DB::statement("ALTER TABLE production_orders DROP COLUMN quantity_received");
        echo "[✓] Столбец quantity_received удален\n";
    } else {
        echo "[!] Столбец quantity_received не найден\n";
    }
} catch (\Exception $e) {
    echo "[✗] Ошибка: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n[*] Проверка структуры таблицы production_orders:\n";
$columns = DB::select("DESCRIBE production_orders");
foreach ($columns as $col) {
    echo "  ✓ {$col->Field} ({$col->Type})\n";
}

echo "\n[✓] Структура обновлена!\n";
