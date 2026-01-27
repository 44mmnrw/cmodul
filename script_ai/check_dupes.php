<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Detail;

echo "Всего записей в таблице details: " . Detail::count() . "\n\n";

echo "Дублирующиеся по названию:\n";
$dupes = Detail::selectRaw('name, COUNT(*) as cnt')
    ->groupBy('name')
    ->having('cnt', '>', 1)
    ->get();

if ($dupes->count() === 0) {
    echo "Дублей нет!\n";
} else {
    foreach ($dupes as $d) {
        echo "  - " . $d->name . " (ID: ?, повторяется " . $d->cnt . " раз)\n";
    }
}

echo "\nВсе записи с ID > 76 (в CSV только до 76):\n";
Detail::where('id', '>', 76)->select('id', 'name', 'scu')->get()->each(function($d) {
    echo "  ID " . $d->id . ": " . $d->name . " (" . $d->scu . ")\n";
});
