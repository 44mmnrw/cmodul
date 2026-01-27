<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Models\Detail;

echo "Total details: " . Detail::count() . "\n\n";
echo "Last 10 details:\n";
Detail::latest()->limit(10)->get(['id', 'name'])->each(function($d) {
    echo $d->id . ': ' . $d->name . "\n";
});

echo "\n\nChecking detail 145:\n";
$detail = Detail::find(145);
if ($detail) {
    echo "Found: " . $detail->name . "\n";
    echo "Places count: " . $detail->places()->count() . "\n";
} else {
    echo "Detail 145 NOT FOUND\n";
}

echo "\nChecking detail 68:\n";
$detail68 = Detail::find(68);
if ($detail68) {
    echo "Found: " . $detail68->name . "\n";
    echo "Places count: " . $detail68->places()->count() . "\n";
} else {
    echo "Detail 68 NOT FOUND\n";
}

echo "=== Details Info ===\n";
$count = \App\Models\Detail::count();
echo "Total details: $count\n\n";

if ($count > 0) {
    $details = \App\Models\Detail::take(5)->get();
    foreach ($details as $detail) {
        echo "- {$detail->name} (ID: {$detail->id})\n";
        echo "  Category: " . ($detail->category ? $detail->category->name : 'N/A') . "\n";
        echo "  URL: /details/{$detail->id}\n\n";
    }
}
