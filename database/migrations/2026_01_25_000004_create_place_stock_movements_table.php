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
        Schema::create('place_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_id')->constrained('places')->onDelete('cascade');
            $table->enum('type', ['incoming', 'outgoing'])->comment('Приход или расход');
            $table->integer('quantity');
            $table->string('reference')->nullable()->comment('Номер документа/заказа');
            $table->text('description')->nullable()->comment('Описание операции');
            $table->date('movement_date');
            $table->timestamps();

            $table->index(['place_id', 'movement_date']);
            $table->index(['type', 'movement_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('place_stock_movements');
    }
};
