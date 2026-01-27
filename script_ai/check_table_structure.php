<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== place_detail Table Structure ===\n";
$columns = \DB::select("DESCRIBE place_detail");
foreach ($columns as $col) {
    echo "{$col->Field} ({$col->Type})\n";
}

echo "\n=== Sample place_detail records ===\n";
$records = \DB::table('place_detail')->limit(5)->get();
foreach ($records as $rec) {
    echo "detail_id: {$rec->detail_id}, place_id: {$rec->place_id}, quantity: {$rec->quantity}\n";
}
