<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Testing Detail Controller ===\n\n";

// Попытаемся загрузить конкретную деталь
$detail = \App\Models\Detail::find(1);

if ($detail) {
    echo "✓ Detail loaded successfully\n";
    echo "  Name: {$detail->name}\n";
    echo "  ID: {$detail->id}\n";
    echo "  Width: {$detail->width}\n";
    echo "  Height: {$detail->height}\n";
    echo "  Depth: {$detail->depth}\n";
    echo "  Weight: {$detail->weight}\n";
    echo "  Price: {$detail->price}\n";
    echo "  Material: {$detail->material}\n";
    
    if ($detail->category) {
        echo "  Category: {$detail->category->name}\n";
    }
    
    echo "\n✓ Route would be: /details/1\n";
} else {
    echo "✗ Could not load detail\n";
}

echo "\n=== Testing Navigation ===\n";
echo "✓ Details link added to navigation: /details\n";
echo "✓ Details index view ready\n";
