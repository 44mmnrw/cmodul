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
        DB::statement("ALTER TABLE products MODIFY id BIGINT UNSIGNED AUTO_INCREMENT COMMENT 'Уникальный идентификатор товара'");
        DB::statement("ALTER TABLE products MODIFY scu VARCHAR(255) NULL COMMENT 'SKU или артикул товара'");
        DB::statement("ALTER TABLE products MODIFY name VARCHAR(255) NOT NULL COMMENT 'Название товара'");
        DB::statement("ALTER TABLE products MODIFY min_quantity INT NULL COMMENT 'Минимальный уровень остатка'");
        DB::statement("ALTER TABLE products MODIFY description TEXT NULL COMMENT 'Описание товара'");
        DB::statement("ALTER TABLE products MODIFY material VARCHAR(255) NULL COMMENT 'Материал (для конфигураций)'");
        DB::statement("ALTER TABLE products MODIFY product_type_id BIGINT UNSIGNED NULL COMMENT 'Тип товара: 1=Конфигурация, 2=Компонент'");
        DB::statement("ALTER TABLE products MODIFY category_id BIGINT UNSIGNED NULL COMMENT 'Категория товара'");
        DB::statement("ALTER TABLE products MODIFY source_id BIGINT UNSIGNED NULL COMMENT 'Источник/поставщик'");
        DB::statement("ALTER TABLE products MODIFY weight DECIMAL(10,3) NULL COMMENT 'Вес в килограммах'");
        DB::statement("ALTER TABLE products MODIFY height DECIMAL(10,3) NULL COMMENT 'Высота в сантиметрах'");
        DB::statement("ALTER TABLE products MODIFY width DECIMAL(10,3) NULL COMMENT 'Ширина в сантиметрах'");
        DB::statement("ALTER TABLE products MODIFY depth DECIMAL(10,3) NULL COMMENT 'Глубина в сантиметрах'");
        DB::statement("ALTER TABLE products MODIFY created_at TIMESTAMP NULL COMMENT 'Время создания'");
        DB::statement("ALTER TABLE products MODIFY updated_at TIMESTAMP NULL COMMENT 'Время обновления'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE products MODIFY id BIGINT UNSIGNED AUTO_INCREMENT");
        DB::statement("ALTER TABLE products MODIFY scu VARCHAR(255) NULL");
        DB::statement("ALTER TABLE products MODIFY name VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE products MODIFY min_quantity INT NULL");
        DB::statement("ALTER TABLE products MODIFY description TEXT NULL");
        DB::statement("ALTER TABLE products MODIFY material VARCHAR(255) NULL");
        DB::statement("ALTER TABLE products MODIFY product_type_id BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE products MODIFY category_id BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE products MODIFY source_id BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE products MODIFY weight DECIMAL(10,3) NULL");
        DB::statement("ALTER TABLE products MODIFY height DECIMAL(10,3) NULL");
        DB::statement("ALTER TABLE products MODIFY width DECIMAL(10,3) NULL");
        DB::statement("ALTER TABLE products MODIFY depth DECIMAL(10,3) NULL");
        DB::statement("ALTER TABLE products MODIFY created_at TIMESTAMP NULL");
        DB::statement("ALTER TABLE products MODIFY updated_at TIMESTAMP NULL");
    }
};
