<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // users таблица
        DB::statement("ALTER TABLE users MODIFY id BIGINT UNSIGNED AUTO_INCREMENT COMMENT 'Уникальный идентификатор пользователя'");
        DB::statement("ALTER TABLE users MODIFY name VARCHAR(255) NOT NULL COMMENT 'Полное имя пользователя'");
        DB::statement("ALTER TABLE users MODIFY email VARCHAR(255) NOT NULL COMMENT 'Электронная почта (уникальна)'");
        DB::statement("ALTER TABLE users MODIFY email_verified_at TIMESTAMP NULL COMMENT 'Дата и время подтверждения почты'");
        DB::statement("ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL COMMENT 'Захеш пароля (bcrypt)'");
        DB::statement("ALTER TABLE users MODIFY remember_token VARCHAR(100) NULL COMMENT 'Токен для функции запомнить меня'");
        DB::statement("ALTER TABLE users MODIFY created_at TIMESTAMP NULL COMMENT 'Время создания'");
        DB::statement("ALTER TABLE users MODIFY updated_at TIMESTAMP NULL COMMENT 'Время обновления'");

        // password_reset_tokens таблица
        DB::statement("ALTER TABLE password_reset_tokens MODIFY email VARCHAR(255) NOT NULL COMMENT 'Email для сброса пароля'");
        DB::statement("ALTER TABLE password_reset_tokens MODIFY token VARCHAR(255) NOT NULL COMMENT 'Токен сброса пароля'");
        DB::statement("ALTER TABLE password_reset_tokens MODIFY created_at TIMESTAMP NULL COMMENT 'Время создания токена'");

        // sessions таблица
        DB::statement("ALTER TABLE sessions MODIFY id VARCHAR(255) NOT NULL COMMENT 'ID сессии'");
        DB::statement("ALTER TABLE sessions MODIFY user_id BIGINT UNSIGNED NULL COMMENT 'Ссылка на пользователя'");
        DB::statement("ALTER TABLE sessions MODIFY ip_address VARCHAR(45) NULL COMMENT 'IP адрес клиента'");
        DB::statement("ALTER TABLE sessions MODIFY user_agent TEXT NULL COMMENT 'User agent браузера'");
        DB::statement("ALTER TABLE sessions MODIFY payload LONGTEXT NOT NULL COMMENT 'Зашифрованные данные сессии'");
        DB::statement("ALTER TABLE sessions MODIFY last_activity INT NOT NULL COMMENT 'Временная метка последней активности'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем комментарии
        DB::statement("ALTER TABLE users MODIFY id BIGINT UNSIGNED AUTO_INCREMENT");
        DB::statement("ALTER TABLE users MODIFY name VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE users MODIFY email VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE users MODIFY email_verified_at TIMESTAMP NULL");
        DB::statement("ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE users MODIFY remember_token VARCHAR(100) NULL");
        DB::statement("ALTER TABLE users MODIFY created_at TIMESTAMP NULL");
        DB::statement("ALTER TABLE users MODIFY updated_at TIMESTAMP NULL");

        DB::statement("ALTER TABLE password_reset_tokens MODIFY email VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE password_reset_tokens MODIFY token VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE password_reset_tokens MODIFY created_at TIMESTAMP NULL");

        DB::statement("ALTER TABLE sessions MODIFY id VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE sessions MODIFY user_id BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE sessions MODIFY ip_address VARCHAR(45) NULL");
        DB::statement("ALTER TABLE sessions MODIFY user_agent TEXT NULL");
        DB::statement("ALTER TABLE sessions MODIFY payload LONGTEXT NOT NULL");
        DB::statement("ALTER TABLE sessions MODIFY last_activity INT NOT NULL");
    }
};
