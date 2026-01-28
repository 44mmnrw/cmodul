<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "⚠️  ПРОВЕРКА СОСТОЯНИЯ МИГРАЦИЙ И ДАННЫХ\n";
echo str_repeat("=", 70) . "\n\n";

// Проверяем миграции
echo "📋 Миграции stock:\n";
$migrations = DB::table('migrations')->where('migration', 'like', '%stock%')->get();
foreach ($migrations as $m) {
    echo "  • {$m->migration} (batch: {$m->batch})\n";
}

// Проверяем, есть ли данные в stocks
echo "\n📊 Данные в таблице stocks:\n";
try {
    $stocks = DB::table('stocks')->count();
    echo "  Строк в stocks: {$stocks}\n";
    if ($stocks > 0) {
        $sample = DB::table('stocks')->limit(3)->get();
        foreach ($sample as $s) {
            echo "    - product_id: {$s->product_id}, quantity: {$s->quantity}\n";
        }
    }
} catch (Exception $e) {
    echo "  ❌ Таблица пуста или ошибка: {$e->getMessage()}\n";
}

// Проверяем, есть ли данные в stock_movements
echo "\n📈 Данные в таблице stock_movements:\n";
try {
    $movements = DB::table('stock_movements')->count();
    echo "  Строк в stock_movements: {$movements}\n";
    if ($movements > 0) {
        echo "  ✅ ДАННЫЕ ЕСТЬ!\n";
    } else {
        echo "  ⚠️  Таблица пуста\n";
    }
} catch (Exception $e) {
    echo "  ❌ Таблица не существует или ошибка: {$e->getMessage()}\n";
}
