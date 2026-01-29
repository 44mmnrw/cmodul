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
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary()->comment('Уникальный ключ кеша');
            $table->mediumText('value')->comment('Значение кеша (сериализованное)');
            $table->integer('expiration')->index()->comment('Время истечения кеша (UNIX timestamp)');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary()->comment('Ключ блокировки кеша');
            $table->string('owner')->comment('Владелец блокировки');
            $table->integer('expiration')->index()->comment('Время истечения блокировки (UNIX timestamp)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
