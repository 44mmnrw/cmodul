<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id()->comment('Уникальный идентификатор очередного задания');
            $table->string('queue')->index()->comment('Название очереди');
            $table->longText('payload')->comment('Сериализованные данные задания');
            $table->unsignedTinyInteger('attempts')->comment('Количество попыток выполнения');
            $table->unsignedInteger('reserved_at')->nullable()->comment('Время, когда работник зарезервировал задание');
            $table->unsignedInteger('available_at')->comment('Время, когда задание станет доступным для обработки');
            $table->unsignedInteger('created_at')->comment('Время создания задания (UNIX timestamp)');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary()->comment('Уникальный идентификатор группы заданий');
            $table->string('name')->comment('Название пакета заданий');
            $table->integer('total_jobs')->comment('Всего заданий в пакете');
            $table->integer('pending_jobs')->comment('Оставшихся заданий');
            $table->integer('failed_jobs')->comment('Количество неудачных заданий');
            $table->longText('failed_job_ids')->comment('JSON список ID неудачных заданий');
            $table->mediumText('options')->nullable()->comment('Опции пакета (JSON)');
            $table->integer('cancelled_at')->nullable()->comment('Время отмены пакета (UNIX timestamp)');
            $table->integer('created_at')->comment('Время создания пакета (UNIX timestamp)');
            $table->integer('finished_at')->nullable()->comment('Время завершения пакета (UNIX timestamp)');
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id()->comment('Уникальный идентификатор отказавшегося задания');
            $table->string('uuid')->unique()->comment('Уникальный UUID задания');
            $table->text('connection')->comment('Соединение БД для задания');
            $table->text('queue')->comment('Названи очереди');
            $table->longText('payload')->comment('Данные задания (JSON)');
            $table->longText('exception')->comment('Стек трассировки исключения');
            $table->timestamp('failed_at')->useCurrent()->comment('Время сбоя задания');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
