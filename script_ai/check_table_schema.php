<?php
require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Проверяем структуру cabinet_place
echo "=== СТРУКТУРА cabinet_place ===\n";
if (Schema::hasTable('cabinet_place')) {
    $columns = Schema::getColumns('cabinet_place');
    foreach ($columns as $col) {
        echo $col['name'] . " | " . $col['type'] . " | nullable: " . ($col['nullable'] ? 'true' : 'false') . "\n";
    }
} else {
    echo "Таблица cabinet_place не существует\n";
}

echo "\n=== СТРУКТУРА place_detail ===\n";
if (Schema::hasTable('place_detail')) {
    $columns = Schema::getColumns('place_detail');
    foreach ($columns as $col) {
        echo $col['name'] . " | " . $col['type'] . " | nullable: " . ($col['nullable'] ? 'true' : 'false') . "\n";
    }
} else {
    echo "Таблица place_detail не существует\n";
}

echo "\n=== СТРУКТУРА details ===\n";
if (Schema::hasTable('details')) {
    $columns = Schema::getColumns('details');
    foreach ($columns as $col) {
        echo $col['name'] . " | " . $col['type'] . " | nullable: " . ($col['nullable'] ? 'true' : 'false') . "\n";
    }
} else {
    echo "Таблица details не существует\n";
}
