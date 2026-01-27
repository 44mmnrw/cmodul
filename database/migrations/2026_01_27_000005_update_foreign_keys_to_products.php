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
        // Обновляем external key ссылки в других таблицах на 'products' вместо 'details'
        
        // Проверим какие FK существуют в configs таблице
        $foreignKeys = \DB::select("SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME='configs' AND COLUMN_NAME IN ('master_id', 'slave_id') 
            AND REFERENCED_TABLE_NAME IS NOT NULL");
        
        // configs таблица - обновляем оба внешних ключа только если они существуют
        Schema::table('configs', function (Blueprint $table) {
            // Получим список существующих FK
            $constraints = \DB::select(
                "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS 
                WHERE CONSTRAINT_SCHEMA = ? AND TABLE_NAME = ?",
                [\DB::getDatabaseName(), 'configs']
            );
            
            $constraintNames = array_column($constraints, 'CONSTRAINT_NAME');
            
            // Удаляем старые FK если они существуют
            $oldForeignKeys = ['cabinet_place_cabinet_detail_id_foreign', 'cabinet_place_place_detail_id_foreign'];
            foreach ($oldForeignKeys as $fk) {
                if (in_array($fk, $constraintNames)) {
                    $table->dropForeign($fk);
                }
            }
            
            // Удаляем новые FK если они существуют (чтобы пересоздать)
            $newForeignKeys = ['configs_master_id_foreign', 'configs_slave_id_foreign'];
            foreach ($newForeignKeys as $fk) {
                if (in_array($fk, $constraintNames)) {
                    $table->dropForeign($fk);
                }
            }
            
            // Добавляем новые ссылки на 'products'
            $table->foreign('master_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
            
            $table->foreign('slave_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });

        // places таблица - если есть detail_id
        if (Schema::hasColumn('places', 'detail_id')) {
            Schema::table('places', function (Blueprint $table) {
                $constraints = \DB::select(
                    "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS 
                    WHERE CONSTRAINT_SCHEMA = ? AND TABLE_NAME = ?",
                    [\DB::getDatabaseName(), 'places']
                );
                
                $constraintNames = array_column($constraints, 'CONSTRAINT_NAME');
                
                // Удаляем если существует
                $fkName = 'places_detail_id_foreign';
                if (in_array($fkName, $constraintNames)) {
                    $table->dropForeign($fkName);
                }
                
                $table->foreign('detail_id')
                    ->references('id')
                    ->on('products')
                    ->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        // Откатываем обратно - но место уже переименовано из place_detail в configs
        // Не можем откатиться назад без полной переделки миграций переименования таблиц
        // Поэтому down() просто ничего не делает
    }
};
