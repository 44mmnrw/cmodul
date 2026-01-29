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
        DB::statement("ALTER TABLE configs MODIFY id BIGINT UNSIGNED AUTO_INCREMENT COMMENT 'Уникальный идентификатор конфигурации'");
        DB::statement("ALTER TABLE configs MODIFY master_id BIGINT UNSIGNED NULL COMMENT 'ID конфигурации (Type 1)'");
        DB::statement("ALTER TABLE configs MODIFY slave_id BIGINT UNSIGNED NULL COMMENT 'ID компонента (Type 2)'");
        DB::statement("ALTER TABLE configs MODIFY quantity INT NOT NULL DEFAULT 1 COMMENT 'Количество компонента в конфигурации'");
        DB::statement("ALTER TABLE configs MODIFY created_at TIMESTAMP NULL COMMENT 'Время создания'");
        DB::statement("ALTER TABLE configs MODIFY updated_at TIMESTAMP NULL COMMENT 'Время обновления'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE configs MODIFY id BIGINT UNSIGNED AUTO_INCREMENT");
        DB::statement("ALTER TABLE configs MODIFY master_id BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE configs MODIFY slave_id BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE configs MODIFY quantity INT NOT NULL DEFAULT 1");
        DB::statement("ALTER TABLE configs MODIFY created_at TIMESTAMP NULL");
        DB::statement("ALTER TABLE configs MODIFY updated_at TIMESTAMP NULL");
    }
};
