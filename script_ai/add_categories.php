<?php
/**
 * Скрипт добавления базовых категорий
 * Использование: php script_ai/add_categories.php
 */

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;

$categories = [
    ['name' => 'Упаковка', 'color' => '#3B82F6'],      // Blue
    ['name' => 'Метизы', 'color' => '#EF4444'],        // Red
    ['name' => 'Лист', 'color' => '#10B981'],          // Green
];

echo "📦 Добавляем категории...\n\n";

$count = 0;
$errors = 0;

foreach ($categories as $cat) {
    try {
        Category::create($cat);
        $count++;
        echo "✓ Добавлена категория: {$cat['name']} ({$cat['color']})\n";
    } catch (\Exception $e) {
        $errors++;
        echo "✗ Ошибка при добавлении '{$cat['name']}': " . $e->getMessage() . "\n";
    }
}

echo "\n";
echo "✅ Готово!\n";
echo "📊 Добавлено: {$count} категорий\n";
echo "⚠️  Ошибок: {$errors}\n";
