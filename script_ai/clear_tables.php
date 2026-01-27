<?php
/**
 * Скрипт очистки таблиц details и place_detail
 * Удаляет все данные и сбрасывает auto_increment
 */

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🗑️  Очищаем таблицы...\n\n";

try {
    // Отключаем проверку иностранных ключей
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    // Очищаем place_detail
    DB::table('place_detail')->truncate();
    echo "✓ Таблица place_detail очищена (автоинкремент сброшен)\n";

    // Очищаем details
    DB::table('details')->truncate();
    echo "✓ Таблица details очищена (автоинкремент сброшен)\n";

    // Включаем проверку иностранных ключей обратно
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    echo "\n✅ Успешно!\n";
    echo "📊 Таблицы очищены и готовы к новому импорту\n";

} catch (\Exception $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
    exit(1);
}
