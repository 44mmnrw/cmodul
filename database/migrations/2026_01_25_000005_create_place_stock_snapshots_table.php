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
        Schema::create('place_stock_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_id')->constrained('places')->onDelete('cascade');
            $table->date('snapshot_date')->comment('Дата состояния');
            $table->integer('quantity')->comment('Остаток на эту дату');
            $table->timestamps();

            $table->index(['place_id', 'snapshot_date']);
            $table->unique(['place_id', 'snapshot_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('place_stock_snapshots');
    }
};
