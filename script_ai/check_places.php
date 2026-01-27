<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Place;

echo "Всего мест: " . Place::count() . "\n\n";
echo "Первые 10 мест:\n";
Place::limit(10)->get(['id', 'place_id', 'name'])->each(function($p) {
    echo "ID: " . $p->id . ", place_id: " . $p->place_id . ", name: " . $p->name . "\n";
});

echo "\n\nПопытка найти место с ID=1:\n";
$place1 = Place::find(1);
if ($place1) {
    echo "Found! place_id: " . $place1->place_id . ", name: " . $place1->name . "\n";
} else {
    echo "NOT FOUND\n";
}

echo "\nПопытка найти место с place_id='1':\n";
$place_str = Place::where('place_id', '1')->first();
if ($place_str) {
    echo "Found! id: " . $place_str->id . ", name: " . $place_str->name . "\n";
} else {
    echo "NOT FOUND\n";
}
