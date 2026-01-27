<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$csvFile = __DIR__ . '/../data/place_detail.csv';

if (!file_exists($csvFile)) {
    echo "CSV файл не найден: $csvFile\n";
    exit(1);
}

$handle = fopen($csvFile, 'r');
if (!$handle) {
    echo "Не могу открыть файл: $csvFile\n";
    exit(1);
}

$header = fgetcsv($handle, 0, ';');
echo "Headers: " . implode(', ', $header) . "\n";

$count = 0;
$errors = [];

while (($row = fgetcsv($handle, 0, ';')) !== false) {
    if (empty($row[0]) || empty($row[1])) {
        continue;
    }
    
    $place_id = trim($row[0], '"');
    $detail_id = trim($row[1], '"');
    
    // Проверяем что место существует
    $placeExists = DB::table('places')->where('place_id', $place_id)->exists();
    if (!$placeExists) {
        $errors[] = "Место $place_id не найдено";
        continue;
    }
    
    // Проверяем что деталь существует
    $detailExists = DB::table('details')->where('id', $detail_id)->exists();
    if (!$detailExists) {
        $errors[] = "Деталь $detail_id не найдена";
        continue;
    }
    
    // Вставляем
    DB::table('place_detail')->insert([
        'place_id' => $place_id,
        'detail_id' => $detail_id,
        'quantity' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    $count++;
}

fclose($handle);

echo "\n=== РЕЗУЛЬТАТ ===\n";
echo "Импортировано: $count записей\n";

if (!empty($errors)) {
    echo "Ошибок: " . count($errors) . "\n";
    foreach (array_slice($errors, 0, 10) as $error) {
        echo "  - $error\n";
    }
    if (count($errors) > 10) {
        echo "  ... и ещё " . (count($errors) - 10) . " ошибок\n";
    }
}

echo "\nВсего записей в таблице: " . DB::table('place_detail')->count() . "\n";
echo "\nДеталей с местами: " . DB::table('details')
    ->whereIn('id', DB::table('place_detail')->distinct('detail_id')->pluck('detail_id'))
    ->count() . "\n";
