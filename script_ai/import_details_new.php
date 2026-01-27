<?php
/**
 * Скрипт импорта деталей из обновленного CSV
 * Использование: php script_ai/import_details_new.php
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
$header = fgetcsv($file, 1000, ';');

echo "📥 Начинаем импорт из {$filePath}...\n";
echo "📋 Заголовок: " . implode(', ', $header) . "\n\n";

$count = 0;
$errors = 0;
$line = 1;

while (($row = fgetcsv($file, 1000, ';')) !== false) {
    $line++;
    
    if (count($row) < 3 || empty($row[0])) {
        continue;
    }

    $id = (int) trim($row[0], ' "');
    $scu = trim($row[1], ' "');
    $name = trim($row[2], ' "');
    $weight = !empty($row[3]) ? (float) str_replace(',', '.', trim($row[3], ' "')) : null;
    $width = !empty($row[4]) ? (float) str_replace(',', '.', trim($row[4], ' "')) : null;
    $depth = !empty($row[5]) ? (float) str_replace(',', '.', trim($row[5], ' "')) : null;
    $height = !empty($row[6]) ? (float) str_replace(',', '.', trim($row[6], ' "')) : null;

    try {
        Detail::create([
            'id' => $id,
            'scu' => $scu,
            'name' => $name,
            'weight' => $weight,
            'width' => $width,
            'depth' => $depth,
            'height' => $height,
            'description' => null,
        ]);
        $count++;
        echo "✓ Строка {$line}: ID={$id}, SCU={$scu}, Name={$name}\n";
        
    } catch (\Exception $e) {
        $errors++;
        echo "✗ Строка {$line}: {$scu} - " . $e->getMessage() . "\n";
    }
}

fclose($file);

echo "\n";
echo "✅ Импорт завершён!\n";
echo "📊 Импортировано: {$count} деталей\n";
echo "⚠️  Ошибок: {$errors}\n";
