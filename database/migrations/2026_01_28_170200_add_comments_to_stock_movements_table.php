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
        DB::statement("ALTER TABLE stock_movements MODIFY id BIGINT UNSIGNED AUTO_INCREMENT COMMENT 'Уникальный идентификатор движения'");
        DB::statement("ALTER TABLE stock_movements MODIFY product_id BIGINT UNSIGNED NOT NULL COMMENT 'Ссылка на товар'");
        DB::statement("ALTER TABLE stock_movements MODIFY movement_type ENUM('IN', 'OUT', 'RESERVE', 'RELEASE', 'ADJUST') NOT NULL COMMENT 'Тип движения: приход, отгрузка, резерв, отмена резерва, корректировка'");
        DB::statement("ALTER TABLE stock_movements MODIFY quantity INT NOT NULL COMMENT 'Количество единиц в движении'");
        DB::statement("ALTER TABLE stock_movements MODIFY reference_type VARCHAR(255) NULL COMMENT 'Тип связанного документа (ProductionOrder, Shipment, Manual)'");
        DB::statement("ALTER TABLE stock_movements MODIFY reference_id BIGINT UNSIGNED NULL COMMENT 'ID связанного документа'");
        DB::statement("ALTER TABLE stock_movements MODIFY reason TEXT NULL COMMENT 'Примечания и причины для операции'");
        DB::statement("ALTER TABLE stock_movements MODIFY balance_before INT NULL COMMENT 'Остаток ДО операции (для аудита)'");
        DB::statement("ALTER TABLE stock_movements MODIFY balance_after INT NULL COMMENT 'Остаток ПОСЛЕ операции (для аудита)'");
        DB::statement("ALTER TABLE stock_movements MODIFY created_at TIMESTAMP NULL COMMENT 'Время создания'");
        DB::statement("ALTER TABLE stock_movements MODIFY updated_at TIMESTAMP NULL COMMENT 'Время обновления'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE stock_movements MODIFY id BIGINT UNSIGNED AUTO_INCREMENT");
        DB::statement("ALTER TABLE stock_movements MODIFY product_id BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE stock_movements MODIFY movement_type ENUM('IN', 'OUT', 'RESERVE', 'RELEASE', 'ADJUST') NOT NULL");
        DB::statement("ALTER TABLE stock_movements MODIFY quantity INT NOT NULL");
        DB::statement("ALTER TABLE stock_movements MODIFY reference_type VARCHAR(255) NULL");
        DB::statement("ALTER TABLE stock_movements MODIFY reference_id BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE stock_movements MODIFY reason TEXT NULL");
        DB::statement("ALTER TABLE stock_movements MODIFY balance_before INT NULL");
        DB::statement("ALTER TABLE stock_movements MODIFY balance_after INT NULL");
        DB::statement("ALTER TABLE stock_movements MODIFY created_at TIMESTAMP NULL");
        DB::statement("ALTER TABLE stock_movements MODIFY updated_at TIMESTAMP NULL");
    }
};
