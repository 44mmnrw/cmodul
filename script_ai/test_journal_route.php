<?php

// Быстрая проверка доступности роута
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

try {
    $request = \Illuminate\Http\Request::create('/receipts/journal', 'GET');
    $response = $kernel->handle($request);
    
    echo "✅ Маршрут /receipts/journal доступен\n";
    echo "Статус: " . $response->status() . "\n";
    
    if ($response->status() === 200) {
        echo "✅ Страница загружается корректно\n";
    }
} catch (\Exception $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
}
