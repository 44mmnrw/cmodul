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
        Schema::table('production_order_statuses', function (Blueprint $table) {
            $table->dropUnique('production_order_statuses_code_unique');
            $table->dropColumn('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_order_statuses', function (Blueprint $table) {
            $table->string('code')->unique()->comment('Код статуса');
        });
    }
};
