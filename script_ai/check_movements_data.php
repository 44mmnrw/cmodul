<?php

use App\Models\StockMovement;

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

try {
    $movements = StockMovement::where('movement_type', 'IN')
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
    
    echo "✅ Найдено приходов: " . StockMovement::where('movement_type', 'IN')->count() . "\n";
    foreach ($movements as $m) {
        echo "ID: {$m->id}, Дата: {$m->created_at}, Кол-во: {$m->quantity}\n";
    }
} catch (\Exception $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
}
