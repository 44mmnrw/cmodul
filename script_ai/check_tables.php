<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = DB::select('SHOW TABLES');
echo "СУЩЕСТВУЮЩИЕ ТАБЛИЦЫ В БД:\n";
echo str_repeat('=', 60) . "\n";

foreach ($tables as $table) {
  $tableName = (array)$table;
  $name = reset($tableName);
  echo "  • $name\n";
}
