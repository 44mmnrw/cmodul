<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "СТОЛБЦЫ ТАБЛИЦЫ stock_movements:\n";
echo str_repeat('=', 70) . "\n";

$columns = DB::select('DESCRIBE stock_movements');
foreach ($columns as $col) {
    echo $col->Field . " (" . $col->Type . ")\n";
}

echo "\nМИГРАЦИИ stock_movements:\n";
echo str_repeat('=', 70) . "\n";

$migrations = DB::table('migrations')
    ->where('migration', 'like', '%stock_movements%')
    ->orWhere('migration', 'like', '%stock%')
    ->orderBy('batch')
    ->get();

foreach ($migrations as $m) {
    $status = $m->batch ? "✅ Ran (batch {$m->batch})" : "❌ Pending";
    echo $m->migration . ": {$status}\n";
}
