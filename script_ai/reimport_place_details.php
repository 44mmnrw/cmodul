<?php
/**
 * Скрипт очистки и переимпорта place_detail
 */

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Place;
use App\Models\Detail;
use Illuminate\Support\Facades\DB;

echo "🗑️  Очищаем таблицу place_detail...\n";

try {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('place_detail')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    echo "✓ Таблица place_detail очищена\n\n";
} catch (\Exception $e) {
    echo "❌ Ошибка при очистке: " . $e->getMessage() . "\n";
    exit(1);
}

// Импорт
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

while (($row = fgetcsv($file, 1000, ';')) !== false) {
    $line++;
    
    if (count($row) < 2 || empty($row[0])) {
        continue;
    }

    $place_id = trim($row[0], ' "');
    $detail_id = (int) trim($row[1], ' "');

    try {
        // Проверяем, что место существует
        $place = Place::where('place_id', $place_id)->firstOrFail();
        
        // Проверяем, что деталь существует
        $detail = Detail::find($detail_id);
        
        if (!$detail) {
            echo "⚠️  Строка {$line}: Деталь с ID {$detail_id} не найдена\n";
            $errors++;
            continue;
        }

        // Вставляем запись в place_detail
        DB::table('place_detail')->insert([
            'place_id' => $place_id,
            'detail_id' => $detail->id,
            'quantity' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $count++;
        
    } catch (\Exception $e) {
        $errors++;
        echo "✗ Строка {$line}: place_id={$place_id}, detail_id={$detail_id} - " . $e->getMessage() . "\n";
    }
}

fclose($file);

echo "\n";
echo "✅ Импорт завершён!\n";
echo "📊 Импортировано: {$count} связей\n";
echo "⚠️  Ошибок: {$errors}\n";
