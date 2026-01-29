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
        DB::statement("ALTER TABLE categories MODIFY id BIGINT UNSIGNED AUTO_INCREMENT COMMENT 'Уникальный идентификатор категории'");
        DB::statement("ALTER TABLE categories MODIFY name VARCHAR(255) NOT NULL UNIQUE COMMENT 'Название категории'");
        DB::statement("ALTER TABLE categories MODIFY color VARCHAR(255) NULL COMMENT 'Цвет для UI (hex код или название)'");
        DB::statement("ALTER TABLE categories MODIFY created_at TIMESTAMP NULL COMMENT 'Время создания'");
        DB::statement("ALTER TABLE categories MODIFY updated_at TIMESTAMP NULL COMMENT 'Время обновления'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE categories MODIFY id BIGINT UNSIGNED AUTO_INCREMENT");
        DB::statement("ALTER TABLE categories MODIFY name VARCHAR(255) NOT NULL UNIQUE");
        DB::statement("ALTER TABLE categories MODIFY color VARCHAR(255) NULL");
        DB::statement("ALTER TABLE categories MODIFY created_at TIMESTAMP NULL");
        DB::statement("ALTER TABLE categories MODIFY updated_at TIMESTAMP NULL");
    }
};
