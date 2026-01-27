<?php
/**
 * Скрипт импорта связей place_detail из CSV
 * Использование: php script_ai/import_place_details.php
 */

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Place;
use App\Models\Detail;
use Illuminate\Support\Facades\DB;

$filePath = 'data/place_detail.csv';

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

// Очищаем старые данные
DB::table('place_detail')->truncate();

while (($row = fgetcsv($file, 1000, ';')) !== false) {
    $line++;
    
    if (count($row) < 3 || empty($row[0])) {
        continue;
    }

    $place_id = trim($row[0], ' "');
    $detail_id = trim($row[1], ' "');
    $quantity = (int) trim($row[2], ' "');

    try {
        // Берем place_id как строку (артикул)
        $place_id_str = trim($row[0], ' "');
        $detail_id_num = trim($row[1], ' "');

        // Проверяем, что место существует
        $place = Place::where('place_id', $place_id_str)->firstOrFail();
        
        // Проверяем, что деталь существует
        $detail = Detail::find($detail_id_num);
        
        if (!$detail) {
            echo "⚠️  Строка {$line}: Деталь с ID {$detail_id_num} не найдена\n";
            $errors++;
            continue;
        }

        // Вставляем запись в place_detail с place_id как строка и quantity
        DB::table('place_detail')->insert([
            'place_id' => $place_id_str,
            'detail_id' => $detail->id,
            'quantity' => $quantity,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $count++;
        echo "✓ Строка {$line}: place_id={$place_id_str}, detail_id={$detail_id_num}, qty={$quantity}\n";
        
    } catch (\Exception $e) {
        $errors++;
        echo "✗ Строка {$line}: {$place_id},{$detail_id} - " . $e->getMessage() . "\n";
    }
}

fclose($file);

echo "\n";
echo "✅ Импорт завершён!\n";
echo "📊 Импортировано: {$count} связей\n";
echo "⚠️  Ошибок: {$errors}\n";
