<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Source;

DB::table('sources')->insert([
    ['name' => 'Собственное производство', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Покупка', 'created_at' => now(), 'updated_at' => now()]
]);

echo "Sources созданы:\n";
Source::all()->each(function($s) {
    echo $s->id . ': ' . $s->name . "\n";
});
