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
        if (!Schema::hasColumn('details', 'price')) {
            Schema::table('details', function (Blueprint $table) {
                $table->decimal('price', 10, 2)->nullable()->after('description');
                $table->decimal('price_per_kg', 10, 2)->nullable()->after('price');
                $table->string('material')->nullable()->after('price_per_kg');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('details', function (Blueprint $table) {
            if (Schema::hasColumn('details', 'price')) {
                $table->dropColumn('price');
            }
            if (Schema::hasColumn('details', 'price_per_kg')) {
                $table->dropColumn('price_per_kg');
            }
            if (Schema::hasColumn('details', 'material')) {
                $table->dropColumn('material');
            }
        });
    }
};
