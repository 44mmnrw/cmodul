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
        DB::statement("ALTER TABLE jobs MODIFY id BIGINT UNSIGNED AUTO_INCREMENT COMMENT 'Уникальный идентификатор очередного задания'");
        DB::statement("ALTER TABLE jobs MODIFY queue VARCHAR(255) NOT NULL COMMENT 'Название очереди'");
        DB::statement("ALTER TABLE jobs MODIFY payload LONGTEXT NOT NULL COMMENT 'Сериализованные данные задания'");
        DB::statement("ALTER TABLE jobs MODIFY attempts TINYINT UNSIGNED NOT NULL COMMENT 'Количество попыток выполнения'");
        DB::statement("ALTER TABLE jobs MODIFY reserved_at INT UNSIGNED NULL COMMENT 'Время, когда работник зарезервировал задание'");
        DB::statement("ALTER TABLE jobs MODIFY available_at INT UNSIGNED NOT NULL COMMENT 'Время, когда задание станет доступным для обработки'");
        DB::statement("ALTER TABLE jobs MODIFY created_at INT UNSIGNED NOT NULL COMMENT 'Время создания задания (UNIX timestamp)'");

        DB::statement("ALTER TABLE job_batches MODIFY id VARCHAR(255) NOT NULL COMMENT 'Уникальный идентификатор группы заданий'");
        DB::statement("ALTER TABLE job_batches MODIFY name VARCHAR(255) NOT NULL COMMENT 'Название пакета заданий'");
        DB::statement("ALTER TABLE job_batches MODIFY total_jobs INT NOT NULL COMMENT 'Всего заданий в пакете'");
        DB::statement("ALTER TABLE job_batches MODIFY pending_jobs INT NOT NULL COMMENT 'Оставшихся заданий'");
        DB::statement("ALTER TABLE job_batches MODIFY failed_jobs INT NOT NULL COMMENT 'Количество неудачных заданий'");
        DB::statement("ALTER TABLE job_batches MODIFY failed_job_ids LONGTEXT NOT NULL COMMENT 'JSON список ID неудачных заданий'");
        DB::statement("ALTER TABLE job_batches MODIFY options MEDIUMTEXT NULL COMMENT 'Опции пакета (JSON)'");
        DB::statement("ALTER TABLE job_batches MODIFY cancelled_at INT NULL COMMENT 'Время отмены пакета (UNIX timestamp)'");
        DB::statement("ALTER TABLE job_batches MODIFY created_at INT NOT NULL COMMENT 'Время создания пакета (UNIX timestamp)'");
        DB::statement("ALTER TABLE job_batches MODIFY finished_at INT NULL COMMENT 'Время завершения пакета (UNIX timestamp)'");

        DB::statement("ALTER TABLE failed_jobs MODIFY id BIGINT UNSIGNED AUTO_INCREMENT COMMENT 'Уникальный идентификатор отказавшегося задания'");
        DB::statement("ALTER TABLE failed_jobs MODIFY uuid VARCHAR(255) NOT NULL UNIQUE COMMENT 'Уникальный UUID задания'");
        DB::statement("ALTER TABLE failed_jobs MODIFY connection TEXT NOT NULL COMMENT 'Соединение БД для задания'");
        DB::statement("ALTER TABLE failed_jobs MODIFY queue TEXT NOT NULL COMMENT 'Названи очереди'");
        DB::statement("ALTER TABLE failed_jobs MODIFY payload LONGTEXT NOT NULL COMMENT 'Данные задания (JSON)'");
        DB::statement("ALTER TABLE failed_jobs MODIFY exception LONGTEXT NOT NULL COMMENT 'Стек трассировки исключения'");
        DB::statement("ALTER TABLE failed_jobs MODIFY failed_at TIMESTAMP NOT NULL COMMENT 'Время сбоя задания'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE jobs MODIFY id BIGINT UNSIGNED AUTO_INCREMENT");
        DB::statement("ALTER TABLE jobs MODIFY queue VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE jobs MODIFY payload LONGTEXT NOT NULL");
        DB::statement("ALTER TABLE jobs MODIFY attempts TINYINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE jobs MODIFY reserved_at INT UNSIGNED NULL");
        DB::statement("ALTER TABLE jobs MODIFY available_at INT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE jobs MODIFY created_at INT UNSIGNED NOT NULL");

        DB::statement("ALTER TABLE job_batches MODIFY id VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE job_batches MODIFY name VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE job_batches MODIFY total_jobs INT NOT NULL");
        DB::statement("ALTER TABLE job_batches MODIFY pending_jobs INT NOT NULL");
        DB::statement("ALTER TABLE job_batches MODIFY failed_jobs INT NOT NULL");
        DB::statement("ALTER TABLE job_batches MODIFY failed_job_ids LONGTEXT NOT NULL");
        DB::statement("ALTER TABLE job_batches MODIFY options MEDIUMTEXT NULL");
        DB::statement("ALTER TABLE job_batches MODIFY cancelled_at INT NULL");
        DB::statement("ALTER TABLE job_batches MODIFY created_at INT NOT NULL");
        DB::statement("ALTER TABLE job_batches MODIFY finished_at INT NULL");

        DB::statement("ALTER TABLE failed_jobs MODIFY id BIGINT UNSIGNED AUTO_INCREMENT");
        DB::statement("ALTER TABLE failed_jobs MODIFY uuid VARCHAR(255) NOT NULL UNIQUE");
        DB::statement("ALTER TABLE failed_jobs MODIFY connection TEXT NOT NULL");
        DB::statement("ALTER TABLE failed_jobs MODIFY queue TEXT NOT NULL");
        DB::statement("ALTER TABLE failed_jobs MODIFY payload LONGTEXT NOT NULL");
        DB::statement("ALTER TABLE failed_jobs MODIFY exception LONGTEXT NOT NULL");
        DB::statement("ALTER TABLE failed_jobs MODIFY failed_at TIMESTAMP NOT NULL");
    }
};
