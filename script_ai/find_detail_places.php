<?php

require __DIR__ . '/../bootstrap/app.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Detail;

$detail = Detail::find(68);

if (!$detail) {
    echo "Деталь не найдена\n";
    exit(1);
}

echo "=== Деталь: {$detail->name} (ID: {$detail->id}) ===\n\n";
echo "Используется в местах:\n";

foreach ($detail->places as $place) {
    echo "- {$place->name} ({$place->place_id}) - Количество: {$place->pivot->quantity}\n";
}

if ($detail->places->count() === 0) {
    echo "Деталь не используется ни в одном месте\n";
}
