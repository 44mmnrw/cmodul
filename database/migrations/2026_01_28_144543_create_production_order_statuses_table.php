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
        Schema::create('production_order_statuses', function (Blueprint $table) {
            $table->id()->comment('Уникальный ID статуса');
            $table->string('code')->unique()->comment('Код статуса (ordering, in_production, ready, completed)');
            $table->string('name')->comment('Название статуса на русском');
            $table->string('color', 20)->default('#6b7280')->comment('HEX цвет для отображения');
            $table->text('description')->nullable()->comment('Описание статуса');
            $table->integer('sort_order')->default(0)->comment('Порядок сортировки');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_order_statuses');
    }
};
