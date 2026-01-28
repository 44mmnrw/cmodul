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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
