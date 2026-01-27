<?php
/**
 * Скрипт импорта деталей из CSV в таблицу details
 * Использование: php script_ai/import_details.php
 */

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Detail;

$filePath = 'data/details.csv';

if (!file_exists($filePath)) {
    echo "❌ Файл не найден: {$filePath}\n";
    exit(1);
}

$file = fopen($filePath, 'r');
$count = 0;
$errors = 0;

echo "📥 Начинаем импорт из {$filePath}...\n";

while (($line = fgets($file)) !== false) {
    $name = trim($line, " \t\n\r\0\x0B\"");
    
    if (empty($name)) {
        continue;
    }

    try {
        Detail::create([
            'name' => $name,
            'weight' => null,
            'height' => null,
            'width' => null,
            'depth' => null,
            'scu' => null,
        ]);
        $count++;
        echo "✓ Импортировано: {$name}\n";
    } catch (\Exception $e) {
        $errors++;
        echo "✗ Ошибка для '{$name}': " . $e->getMessage() . "\n";
    }
}

fclose($file);

echo "\n";
echo "✅ Импорт завершён!\n";
echo "📊 Импортировано: {$count} деталей\n";
echo "⚠️  Ошибок: {$errors}\n";
