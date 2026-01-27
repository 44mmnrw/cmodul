<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Detail;
use App\Models\Place;
use App\Models\PlaceStock;

echo "=== Detail-Place Relationships Check ===\n\n";

// Проверить связь place_detail таблицу
$detailPlaceCount = \DB::table('place_detail')->count();
echo "Total place_detail records: $detailPlaceCount\n\n";

// Проверить несколько деталей
$details = Detail::with('places.stock')->limit(5)->get();

foreach ($details as $detail) {
    echo "Detail: {$detail->name} (ID: {$detail->id})\n";
    echo "  Places count: {$detail->places->count()}\n";
    
    foreach ($detail->places as $place) {
        echo "    - Place: {$place->name} (ID: {$place->id})\n";
        echo "      Place ID field: {$place->place_id}\n";
        echo "      Pivot quantity: " . ($place->pivot->quantity ?? 'NULL') . "\n";
        
        if ($place->stock) {
            echo "      Stock exists: YES\n";
            echo "      Stock quantity: {$place->stock->quantity}\n";
            echo "      Stock ID: {$place->stock->id}\n";
        } else {
            echo "      Stock exists: NO\n";
        }
    }
    echo "\n";
}

// Проверить места, которые не имеют деталей
echo "\n=== Places without details ===\n";
$placesWithoutDetails = Place::whereDoesntHave('details')->limit(5)->get();
echo "Count: " . $placesWithoutDetails->count() . "\n";

foreach ($placesWithoutDetails as $place) {
    echo "  - {$place->name} (ID: {$place->id})\n";
}

// Проверить место с деталями
echo "\n=== Places with details ===\n";
$placesWithDetails = Place::has('details')->limit(5)->get();
echo "Count: " . $placesWithDetails->count() . "\n";

foreach ($placesWithDetails as $place) {
    echo "  - {$place->name} has " . $place->details->count() . " details\n";
}
