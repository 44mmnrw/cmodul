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
        DB::statement("ALTER TABLE cache MODIFY `key` VARCHAR(255) NOT NULL COMMENT 'Уникальный ключ кеша'");
        DB::statement("ALTER TABLE cache MODIFY value MEDIUMTEXT NOT NULL COMMENT 'Значение кеша (сериализованное)'");
        DB::statement("ALTER TABLE cache MODIFY expiration INT NOT NULL COMMENT 'Время истечения кеша (UNIX timestamp)'");

        DB::statement("ALTER TABLE cache_locks MODIFY `key` VARCHAR(255) NOT NULL COMMENT 'Ключ блокировки кеша'");
        DB::statement("ALTER TABLE cache_locks MODIFY owner VARCHAR(255) NOT NULL COMMENT 'Владелец блокировки'");
        DB::statement("ALTER TABLE cache_locks MODIFY expiration INT NOT NULL COMMENT 'Время истечения блокировки (UNIX timestamp)'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE cache MODIFY `key` VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE cache MODIFY value MEDIUMTEXT NOT NULL");
        DB::statement("ALTER TABLE cache MODIFY expiration INT NOT NULL");

        DB::statement("ALTER TABLE cache_locks MODIFY `key` VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE cache_locks MODIFY owner VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE cache_locks MODIFY expiration INT NOT NULL");
    }
};
