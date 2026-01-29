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
        Schema::table('orders', function (Blueprint $table) {
            // Добавить поля, если их еще нет
            if (!Schema::hasColumn('orders', 'order_date')) {
                $table->datetime('order_date')->nullable()->after('order_num');
            }
            if (!Schema::hasColumn('orders', 'planned_date')) {
                $table->datetime('planned_date')->nullable()->after('order_date');
            }
            if (!Schema::hasColumn('orders', 'status')) {
                $table->string('status')->default('Pending')->after('planned_date');
            }
            
            // Удалить старое поле date если оно есть
            if (Schema::hasColumn('orders', 'date')) {
                $table->dropColumn('date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'order_date')) {
                $table->dropColumn('order_date');
            }
            if (Schema::hasColumn('orders', 'planned_date')) {
                $table->dropColumn('planned_date');
            }
            if (Schema::hasColumn('orders', 'status')) {
                $table->dropColumn('status');
            }
            
            $table->date('date')->nullable();
        });
    }
};
