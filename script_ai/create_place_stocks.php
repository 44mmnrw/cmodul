<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Place;
use App\Models\PlaceStock;

echo "=== Creating PlaceStock for all places ===\n";

$places = Place::all();
echo "Total places: " . $places->count() . "\n\n";

$created = 0;
$skipped = 0;

foreach ($places as $place) {
    $stock = PlaceStock::where('place_id', $place->id)->first();
    
    if (!$stock) {
        PlaceStock::create([
            'place_id' => $place->id,
            'quantity' => 100,  // Default quantity
            'reserved_quantity' => 0,
        ]);
        $created++;
        echo "Created stock for: {$place->name}\n";
    } else {
        $skipped++;
    }
}

echo "\nResult:\n";
echo "  Created: $created\n";
echo "  Skipped (already exist): $skipped\n";

// Verify
echo "\n=== Verification ===\n";
$details = \App\Models\Detail::with('places.stock')->limit(3)->get();
foreach ($details as $detail) {
    echo "Detail: {$detail->name}\n";
    foreach ($detail->places as $place) {
        echo "  - {$place->name}: " . ($place->stock ? "Stock exists (qty: {$place->stock->quantity})" : "NO STOCK") . "\n";
    }
}
