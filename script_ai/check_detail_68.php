<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap/app.php';

use App\Models\Detail;
use App\Models\Place;

$detailId = 68;

// Найти деталь
$detail = Detail::find($detailId);

if (!$detail) {
    echo "Деталь с ID $detailId не найдена\n";
    exit;
}

echo "=== Деталь: {$detail->name} (ID: {$detail->id}) ===\n\n";

// Способ 1: Через отношение belongsToMany
$places = $detail->places;

echo "Найдено мест: " . $places->count() . "\n\n";

foreach ($places as $place) {
    echo "ID: {$place->id}\n";
    echo "Код: {$place->place_id}\n";
    echo "Название: {$place->name}\n";
    echo "Количество: {$place->pivot->quantity}\n";
    echo "---\n";
}

// Способ 2: Через прямой запрос в БД для проверки
echo "\n=== Проверка в БД (place_detail) ===\n";
$dbPlaces = \DB::table('place_detail')
    ->where('detail_id', $detailId)
    ->get();

echo "Найдено в place_detail: " . $dbPlaces->count() . " записей\n\n";

foreach ($dbPlaces as $record) {
    $place = Place::where('id', $record->place_id)->first();
    echo "Place ID: {$record->place_id}\n";
    if ($place) {
        echo "Place Code: {$place->place_id}\n";
        echo "Place Name: {$place->name}\n";
    }
    echo "Quantity: {$record->quantity}\n";
    echo "---\n";
}
