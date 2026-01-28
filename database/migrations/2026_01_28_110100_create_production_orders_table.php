<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade')->comment('Type 2 компонент');
            $table->integer('quantity_ordered')->comment('Заказано в производство');
            $table->integer('quantity_received')->default(0)->comment('Получено из производства');
            $table->string('status')->default('ordering')->comment('ordering, in_production, ready, completed');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Индексы для быстрого поиска
            $table->index('product_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_orders');
    }
};
