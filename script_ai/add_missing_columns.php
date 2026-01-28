<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "🔧 ДОБАВЛЕНИЕ ПРОПУЩЕННЫХ ПОЛЕЙ\n";
echo str_repeat("=", 70) . "\n\n";

// 1️⃣ Добавляем document_number
echo "📝 Добавляю поле document_number...\n";
if (!Schema::hasColumn('stock_movements', 'document_number')) {
    Schema::table('stock_movements', function ($table) {
        $table->string('document_number')->nullable()->after('reason');
        $table->index('document_number');
    });
    echo "   ✅ Поле document_number добавлено\n";
} else {
    echo "   ⚠️  Поле document_number уже существует\n";
}

// 2️⃣ Добавляем клиентские поля
echo "\n👤 Добавляю клиентские поля...\n";
if (!Schema::hasColumn('stock_movements', 'client')) {
    Schema::table('stock_movements', function ($table) {
        $table->string('client')->nullable()->after('reason');
        $table->string('address')->nullable()->after('client');
        $table->text('notes')->nullable()->after('address');
    });
    echo "   ✅ Поля client, address, notes добавлены\n";
} else {
    echo "   ⚠️  Клиентские поля уже существуют\n";
}

echo "\n✅ ВСЕ ПОЛЯ ДОБАВЛЕНЫ\n";
