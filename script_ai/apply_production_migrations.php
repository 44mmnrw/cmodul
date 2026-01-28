<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "[*] Добавление min_quantity в таблицу products...\n";

try {
    if (!Schema::hasColumn('products', 'min_quantity')) {
        DB::statement("ALTER TABLE products ADD COLUMN min_quantity INT DEFAULT 0 AFTER name");
        echo "[✓] Столбец min_quantity добавлен\n";
    } else {
        echo "[!] Столбец min_quantity уже существует\n";
    }
} catch (\Exception $e) {
    echo "[✗] Ошибка: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n[*] Создание таблицы production_orders...\n";

try {
    if (!Schema::hasTable('production_orders')) {
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade')->comment('Type 2 компонент');
            $table->integer('quantity_ordered')->comment('Заказано в производство');
            $table->integer('quantity_received')->default(0)->comment('Получено из производства');
            $table->string('status')->default('ordering')->comment('ordering, in_production, ready, completed');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('product_id');
            $table->index('status');
        });
        echo "[✓] Таблица production_orders создана\n";
    } else {
        echo "[!] Таблица production_orders уже существует\n";
    }
} catch (\Exception $e) {
    echo "[✗] Ошибка: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n[*] Регистрация миграций в таблице migrations...\n";

try {
    $batch = DB::table('migrations')->max('batch') + 1;
    
    $migrations = [
        '2026_01_28_110000_add_min_quantity_to_products',
        '2026_01_28_110100_create_production_orders_table',
    ];
    
    foreach ($migrations as $migration) {
        if (!DB::table('migrations')->where('migration', $migration)->exists()) {
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => $batch,
            ]);
            echo "[✓] Зарегистрирована: $migration\n";
        } else {
            echo "[!] Уже зарегистрирована: $migration\n";
        }
    }
} catch (\Exception $e) {
    echo "[✗] Ошибка при регистрации: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n[✓] Все миграции успешно применены!\n";

// Проверим структуру
echo "\n=== Проверка структуры ===\n";
$columns = DB::select("DESCRIBE products");
echo "[*] Поле min_quantity в products:\n";
foreach ($columns as $col) {
    if ($col->Field === 'min_quantity') {
        echo "  ✓ " . $col->Field . " (" . $col->Type . ")\n";
    }
}

$productionOrdersColumns = DB::select("DESCRIBE production_orders");
echo "\n[*] Таблица production_orders имеет столбцы:\n";
foreach ($productionOrdersColumns as $col) {
    echo "  ✓ " . $col->Field . " (" . $col->Type . ")\n";
}
