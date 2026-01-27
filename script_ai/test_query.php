#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$detailId = 68;

echo "=== Тест запроса для detail_id = 68 ===\n\n";

// Тест 1: Просто место
$result1 = DB::table('place_detail')->where('detail_id', $detailId)->get();
echo "1. Без JOIN (place_detail):\n";
echo "Найдено строк: " . $result1->count() . "\n";
if ($result1->count() > 0) {
    echo "Первая строка: " . json_encode($result1->first()) . "\n";
}

echo "\n";

// Тест 2: С JOIN
$result2 = DB::table('place_detail as pd')
    ->join('places as p', 'pd.place_id', '=', 'p.place_id')
    ->where('pd.detail_id', $detailId)
    ->select('p.id', 'p.place_id', 'p.name', 'pd.quantity')
    ->get();

echo "2. С JOIN (place_detail + places):\n";
echo "Найдено строк: " . $result2->count() . "\n";
if ($result2->count() > 0) {
    foreach ($result2 as $row) {
        echo "- ID: {$row->id}, place_id: {$row->place_id}, name: {$row->name}, qty: {$row->quantity}\n";
    }
}

echo "\n";

// Тест 3: Проверим места
$places = DB::table('places')->where('place_id', '44.50.00021')->get();
echo "3. Проверим есть ли место 44.50.00021:\n";
echo "Найдено: " . $places->count() . "\n";
if ($places->count() > 0) {
    echo json_encode($places->first()) . "\n";
}
