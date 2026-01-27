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
        // Переименовываем таблицу details в products
        Schema::rename('details', 'products');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Переименовываем таблицу products обратно в details
        Schema::rename('products', 'details');
    }
};
