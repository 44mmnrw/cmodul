<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE product_types MODIFY id BIGINT UNSIGNED AUTO_INCREMENT COMMENT 'Уникальный идентификатор типа'");
        DB::statement("ALTER TABLE product_types MODIFY name VARCHAR(255) NOT NULL UNIQUE COMMENT 'Название типа: 1=Конфигурация (Type 1), 2=Компонент (Type 2)'");
        DB::statement("ALTER TABLE product_types MODIFY created_at TIMESTAMP NULL COMMENT 'Время создания'");
        DB::statement("ALTER TABLE product_types MODIFY updated_at TIMESTAMP NULL COMMENT 'Время обновления'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE product_types MODIFY id BIGINT UNSIGNED AUTO_INCREMENT");
        DB::statement("ALTER TABLE product_types MODIFY name VARCHAR(255) NOT NULL UNIQUE");
        DB::statement("ALTER TABLE product_types MODIFY created_at TIMESTAMP NULL");
        DB::statement("ALTER TABLE product_types MODIFY updated_at TIMESTAMP NULL");
    }
};
