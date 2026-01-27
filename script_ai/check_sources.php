<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Sources в таблице:\n";
DB::table('sources')->get()->each(function($s) {
    echo $s->id . ': ' . $s->name . "\n";
});
