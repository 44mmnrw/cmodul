#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Detail;
use Illuminate\Support\Facades\DB;

$detailId = 68;

$detail = Detail::find($detailId);

if (!$detail) {
    echo "Деталь с ID $detailId не найдена\n";
    exit;
}

echo "=== Деталь: {$detail->name} (ID: {$detail->id}) ===\n\n";

// Правильный запрос - place_detail.place_id это VARCHAR строка, а не числовой ID!
$places = DB::table('place_detail as pd')
    ->join('places as p', 'pd.place_id', '=', 'p.place_id')
    ->where('pd.detail_id', $detailId)
    ->select('p.id', 'p.place_id', 'p.name', 'pd.quantity')
    ->get();

echo "Найдено мест: " . $places->count() . "\n\n";

foreach ($places as $place) {
    echo "ID: {$place->id}\n";
    echo "Код: {$place->place_id}\n";
    echo "Название: {$place->name}\n";
    echo "Количество: {$place->quantity}\n";
    echo "---\n";
}
