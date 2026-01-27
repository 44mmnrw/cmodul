#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Detail;

$detail = Detail::find(68);

if (!$detail) {
    echo "Деталь не найдена\n";
    exit;
}

echo "=== Detail::find(68)->places ===\n";
echo "Результат load('places'):\n";

$detail->load('places');

echo "Count: " . $detail->places->count() . "\n";
echo "Data:\n";

foreach ($detail->places as $place) {
    echo "- ID: {$place->id}\n";
    echo "  place_id: {$place->place_id}\n";
    echo "  name: {$place->name}\n";
    echo "  pivot->quantity: {$place->pivot->quantity}\n";
}

// Проверим SQL что выполняется
echo "\n=== SQL запросы ===\n";
\DB::enableQueryLog();

$detail2 = Detail::find(68);
$detail2->load('places');

$queries = \DB::getQueryLog();
foreach ($queries as $query) {
    echo $query['query'] . "\n";
    if ($query['bindings']) {
        echo "Bindings: " . json_encode($query['bindings']) . "\n";
    }
}
