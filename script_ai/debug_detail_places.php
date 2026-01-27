#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$detailId = 68;

// Посмотрим как выглядят данные в place_detail
echo "=== Содержимое place_detail для detail_id = 68 ===\n";
$raw = DB::table('place_detail')->where('detail_id', $detailId)->get();

foreach ($raw as $row) {
    echo "place_id: {$row->place_id} (type: " . gettype($row->place_id) . ")\n";
    echo "detail_id: {$row->detail_id}\n";
    echo "quantity: {$row->quantity}\n";
    echo "---\n";
}

// Посмотрим как выглядят места
echo "\n=== Содержимое places ===\n";
$places = DB::table('places')->limit(5)->get();
foreach ($places as $p) {
    echo "id: {$p->id}, place_id: {$p->place_id}\n";
}

// Проверим через Eloquent
echo "\n=== Через Eloquent (Detail::find(68)->places) ===\n";
$detail = \App\Models\Detail::find($detailId);
echo "Найдено мест: " . $detail->places()->count() . "\n";
echo "Отношение работает: " . ($detail->places()->count() > 0 ? "ДА" : "НЕТ") . "\n";
