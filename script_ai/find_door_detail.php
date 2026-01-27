<?php
require __DIR__ . '/../bootstrap/app.php';

$detail = \App\Models\Detail::where('name', 'like', '%Дверь Д-6U%')->first();
if ($detail) {
    echo "Деталь найдена: " . $detail->name . " (ID: " . $detail->id . ")\n";
    echo "Места где используется:\n";
    $places = $detail->places;
    if ($places->count() > 0) {
        foreach ($places as $place) {
            echo "- " . $place->name . " (" . $place->place_id . "), кол-во: " . $place->pivot->quantity . "\n";
        }
    } else {
        echo "Нет мест где используется эта деталь\n";
    }
} else {
    // Поищем все детали с "Дверь" в названии
    echo "Деталь не найдена, ищем похожие:\n";
    $details = \App\Models\Detail::where('name', 'like', '%Дверь%')->get();
    foreach ($details as $d) {
        echo "- " . $d->name . " (ID: " . $d->id . ")\n";
    }
}
