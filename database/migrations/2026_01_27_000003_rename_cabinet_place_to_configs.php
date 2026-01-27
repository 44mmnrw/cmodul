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
        // Переименовываем колонки в cabinet_place
        Schema::table('cabinet_place', function (Blueprint $table) {
            $table->renameColumn('cabinet_detail_id', 'master_id');
            $table->renameColumn('place_detail_id', 'slave_id');
        });

        // Переименовываем таблицу
        Schema::rename('cabinet_place', 'configs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Переименовываем таблицу обратно
        Schema::rename('configs', 'cabinet_place');

        // Переименовываем колонки обратно
        Schema::table('cabinet_place', function (Blueprint $table) {
            $table->renameColumn('master_id', 'cabinet_detail_id');
            $table->renameColumn('slave_id', 'place_detail_id');
        });
    }
};
