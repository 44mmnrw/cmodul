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
        Schema::dropIfExists('place_stock_snapshots');
        Schema::dropIfExists('place_stock_movements');
        Schema::dropIfExists('place_stocks');
        Schema::dropIfExists('places');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('place_id')->unique();
            $table->string('name');
            $table->timestamps();
        });
    }
};
