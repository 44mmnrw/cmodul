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
        DB::statement("ALTER TABLE stocks MODIFY id BIGINT UNSIGNED AUTO_INCREMENT COMMENT 'Уникальный идентификатор'");
        DB::statement("ALTER TABLE stocks MODIFY product_id BIGINT UNSIGNED NOT NULL UNIQUE COMMENT 'Ссылка на товар (Type 2 компонент)'");
        DB::statement("ALTER TABLE stocks MODIFY quantity INT NOT NULL DEFAULT 0 COMMENT 'Всего единиц на складе'");
        DB::statement("ALTER TABLE stocks MODIFY reserved INT NOT NULL DEFAULT 0 COMMENT 'Зарезервировано (не доступно для отгрузки)'");
        DB::statement("ALTER TABLE stocks MODIFY min_quantity INT NOT NULL DEFAULT 0 COMMENT 'Минимальное количество для алерта'");
        DB::statement("ALTER TABLE stocks MODIFY created_at TIMESTAMP NULL COMMENT 'Время создания'");
        DB::statement("ALTER TABLE stocks MODIFY updated_at TIMESTAMP NULL COMMENT 'Время обновления'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE stocks MODIFY id BIGINT UNSIGNED AUTO_INCREMENT");
        DB::statement("ALTER TABLE stocks MODIFY product_id BIGINT UNSIGNED NOT NULL UNIQUE");
        DB::statement("ALTER TABLE stocks MODIFY quantity INT NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE stocks MODIFY reserved INT NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE stocks MODIFY min_quantity INT NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE stocks MODIFY created_at TIMESTAMP NULL");
        DB::statement("ALTER TABLE stocks MODIFY updated_at TIMESTAMP NULL");
    }
};
