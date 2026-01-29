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
        DB::statement("ALTER TABLE production_orders MODIFY id BIGINT UNSIGNED AUTO_INCREMENT COMMENT 'Уникальный идентификатор заказа'");
        DB::statement("ALTER TABLE production_orders MODIFY production_num VARCHAR(255) NULL UNIQUE COMMENT 'Автоматически сгенерированный номер заказа (формат: ПО-0001, ПО-0002)'");
        DB::statement("ALTER TABLE production_orders MODIFY product_id BIGINT UNSIGNED NOT NULL COMMENT 'Ссылка на конфигурацию (Type 1)'");
        DB::statement("ALTER TABLE production_orders MODIFY quantity_ordered INT NOT NULL COMMENT 'Количество единиц заказанных в производство'");
        DB::statement("ALTER TABLE production_orders MODIFY quantity_received INT NOT NULL DEFAULT 0 COMMENT 'Количество единиц полученных из производства'");
        DB::statement("ALTER TABLE production_orders MODIFY status VARCHAR(255) NOT NULL DEFAULT 'ordering' COMMENT 'Статус заказа: ordering (заказ), in_production (в производстве), ready (готов), completed (завершён)'");
        DB::statement("ALTER TABLE production_orders MODIFY planned_date DATE NULL COMMENT 'Плановая дата выполнения заказа'");
        DB::statement("ALTER TABLE production_orders MODIFY notes TEXT NULL COMMENT 'Примечания и комментарии к заказу'");
        DB::statement("ALTER TABLE production_orders MODIFY created_at TIMESTAMP NULL COMMENT 'Время создания'");
        DB::statement("ALTER TABLE production_orders MODIFY updated_at TIMESTAMP NULL COMMENT 'Время обновления'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE production_orders MODIFY id BIGINT UNSIGNED AUTO_INCREMENT");
        DB::statement("ALTER TABLE production_orders MODIFY production_num VARCHAR(255) NULL UNIQUE");
        DB::statement("ALTER TABLE production_orders MODIFY product_id BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE production_orders MODIFY quantity_ordered INT NOT NULL");
        DB::statement("ALTER TABLE production_orders MODIFY quantity_received INT NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE production_orders MODIFY status VARCHAR(255) NOT NULL DEFAULT 'ordering'");
        DB::statement("ALTER TABLE production_orders MODIFY planned_date DATE NULL");
        DB::statement("ALTER TABLE production_orders MODIFY notes TEXT NULL");
        DB::statement("ALTER TABLE production_orders MODIFY created_at TIMESTAMP NULL");
        DB::statement("ALTER TABLE production_orders MODIFY updated_at TIMESTAMP NULL");
    }
};
