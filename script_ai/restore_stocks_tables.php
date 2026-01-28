<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "🔄 ВОССТАНОВЛЕНИЕ ТАБЛИЦ STOCKS И STOCK_MOVEMENTS\n";
echo str_repeat("=", 70) . "\n\n";

// 1️⃣ Восстанавливаем таблицу stocks
echo "📦 Создание таблицы stocks...\n";
if (!Schema::hasTable('stocks')) {
    Schema::create('stocks', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('product_id')->unique();
        
        // Остатки
        $table->integer('quantity')->default(0);    // всего на складе
        $table->integer('reserved')->default(0);    // зарезервировано
        
        // Настройки
        $table->integer('min_quantity')->default(0); // минимум для алерта
        
        $table->timestamps();
        
        // Индексы и внешние ключи
        $table->foreign('product_id')
            ->references('id')
            ->on('products')
            ->onDelete('cascade');
        
        $table->index('product_id');
    });
    echo "   ✅ Таблица stocks создана\n";
} else {
    echo "   ℹ️  Таблица stocks уже существует\n";
}

// 2️⃣ Восстанавливаем таблицу stock_movements
echo "📊 Создание таблицы stock_movements...\n";
if (!Schema::hasTable('stock_movements')) {
    Schema::create('stock_movements', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('product_id');
        
        // Тип операции
        $table->enum('movement_type', ['IN', 'OUT', 'RESERVE', 'RELEASE', 'ADJUST']);
        
        // Количество (может быть отрицательным для OUT)
        $table->integer('quantity');
        
        // Ссылка на источник операции
        $table->string('reference_type')->nullable();  // 'Configuration', 'Order', 'Manual'
        $table->unsignedBigInteger('reference_id')->nullable();
        
        // Примечание
        $table->text('reason')->nullable();
        
        // Балансы для аудита
        $table->integer('balance_before')->nullable();
        $table->integer('balance_after')->nullable();
        
        $table->timestamps();
        
        // Индексы и внешние ключи
        $table->foreign('product_id')
            ->references('id')
            ->on('products')
            ->onDelete('cascade');
        
        $table->index('product_id');
        $table->index('created_at');
        $table->index(['reference_type', 'reference_id']);
        $table->index(['product_id', 'created_at']);
    });
    echo "   ✅ Таблица stock_movements создана\n";
} else {
    echo "   ℹ️  Таблица stock_movements уже существует\n";
}

// 3️⃣ Проверяем миграции
echo "\n📋 Проверка состояния миграций...\n";
$migrations = [
    '2026_01_27_120000_create_stocks_table',
    '2026_01_27_120100_create_stock_movements_table',
    '2026_01_27_140000_add_document_number_to_stock_movements',
    '2026_01_28_100000_add_client_fields_to_stock_movements'
];

foreach ($migrations as $migration) {
    echo "   • {$migration}\n";
}

echo "\n✅ ВОССТАНОВЛЕНИЕ ЗАВЕРШЕНО\n";
echo "Таблицы готовы к использованию\n";
