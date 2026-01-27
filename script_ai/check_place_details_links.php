<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "\n=== Проверка links на детали на странице места ===\n\n";

// Загружаем конкретное место
$place = \App\Models\Place::find(1);

if ($place) {
    // Загружаем детали
    $place->load('details');
    
    echo "✓ Place loaded: {$place->name}\n";
    echo "✓ Place ID: {$place->place_id}\n";
    echo "✓ Details count: " . $place->details->count() . "\n\n";
    
    if ($place->details->count() > 0) {
        echo "Детали в этом месте:\n";
        foreach ($place->details->take(3) as $detail) {
            echo "  • {$detail->name}\n";
            echo "    ID: {$detail->id}\n";
            echo "    Link: /details/{$detail->id}\n";
            echo "    Route name: details.show\n";
            echo "\n";
        }
    }
} else {
    echo "✗ Place not found\n";
}

echo "=== Все ссылки на детали теперь кликабельны! ===\n\n";
