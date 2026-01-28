<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "=== СОЗДАНИЕ ТАБЛИЦ СИСТЕМЫ ПРОГНОЗА ===\n\n";

// 1️⃣ Таблица desired_stock_levels
echo "📋 Создание таблицы desired_stock_levels...\n";
if (!Schema::hasTable('desired_stock_levels')) {
    Schema::create('desired_stock_levels', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('product_id')->unique();
        $table->integer('desired_quantity')->default(10);
        $table->timestamps();

        $table->foreign('product_id')
            ->references('id')
            ->on('products')
            ->onDelete('cascade');
        
        $table->index('product_id');
    });
    echo "   ✅ Таблица создана\n";
} else {
    echo "   ⚠️  Таблица уже существует\n";
}

// 2️⃣ Таблица demand_forecasts
echo "📊 Создание таблицы demand_forecasts...\n";
if (!Schema::hasTable('demand_forecasts')) {
    Schema::create('demand_forecasts', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('product_id');
        $table->tinyInteger('product_type_id');
        
        $table->integer('current_stock_calculated')->default(0);
        $table->integer('desired_stock')->nullable();
        $table->integer('gap')->default(0);
        $table->decimal('gap_percentage', 5, 2)->default(0);
        
        $table->enum('forecast_status', ['OK', 'WARNING', 'CRITICAL', 'OVERSUPPLY'])->default('OK');
        
        $table->unsignedBigInteger('required_for_product_id')->nullable();
        $table->unsignedBigInteger('bottleneck_product_id')->nullable();
        
        $table->timestamp('calculated_at')->useCurrent();
        $table->timestamp('valid_until')->nullable();
        $table->timestamps();

        $table->foreign('product_id')
            ->references('id')
            ->on('products')
            ->onDelete('cascade');
        
        $table->foreign('required_for_product_id')
            ->references('id')
            ->on('products')
            ->onDelete('set null');
        
        $table->foreign('bottleneck_product_id')
            ->references('id')
            ->on('products')
            ->onDelete('set null');

        $table->index('product_id');
        $table->index(['product_id', 'calculated_at']);
        $table->index('forecast_status');
    });
    echo "   ✅ Таблица создана\n";
} else {
    echo "   ⚠️  Таблица уже существует\n";
}

// 3️⃣ Таблица procurement_recommendations
echo "🎯 Создание таблицы procurement_recommendations...\n";
if (!Schema::hasTable('procurement_recommendations')) {
    Schema::create('procurement_recommendations', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('product_id');
        $table->tinyInteger('product_type_id');
        
        $table->integer('recommended_quantity');
        $table->decimal('unit_cost', 12, 2)->nullable();
        $table->decimal('total_cost', 14, 2)->nullable();
        
        $table->enum('priority', ['CRITICAL', 'HIGH', 'MEDIUM', 'LOW', 'OPTIONAL'])->default('MEDIUM');
        $table->text('reason')->nullable();
        
        $table->unsignedBigInteger('source_forecast_id')->nullable();
        $table->unsignedBigInteger('required_for_product_id')->nullable();
        
        $table->enum('status', ['DRAFT', 'APPROVED', 'ORDERED', 'PARTIAL', 'RECEIVED', 'CANCELLED'])->default('DRAFT');
        
        $table->timestamp('ordered_at')->nullable();
        $table->timestamp('expected_delivery')->nullable();
        $table->timestamp('received_at')->nullable();
        
        $table->timestamps();

        $table->foreign('product_id')
            ->references('id')
            ->on('products')
            ->onDelete('cascade');
        
        $table->foreign('required_for_product_id')
            ->references('id')
            ->on('products')
            ->onDelete('set null');

        $table->index('product_id');
        $table->index('status');
        $table->index('priority');
        $table->index(['status', 'priority']);
    });
    echo "   ✅ Таблица создана\n";
} else {
    echo "   ⚠️  Таблица уже существует\n";
}

echo "\n✅ ВСЕ ТАБЛИЦЫ СОЗДАНЫ УСПЕШНО\n";
