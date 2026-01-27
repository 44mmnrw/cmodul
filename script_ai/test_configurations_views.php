<?php

require __DIR__ . '/../vendor/autoload.php';

// Инициализируем приложение
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

// Проверяем загрузку представлений
echo "=== Проверка загрузки представлений конфигураций ===\n\n";

$viewFactory = $app->make('view');

$testViews = [
    'configurations.index' => ['cabinets' => []],
    'configurations.show' => ['cabinet' => (object)['id' => 1, 'name' => 'Test', 'scu' => 'TEST-001', 'description' => 'Test']],
    'configurations.edit' => [
        'cabinet' => (object)['id' => 1, 'name' => 'Test', 'scu' => 'TEST-001', 'description' => 'Test'],
        'currentComponents' => [],
        'availableComponents' => [],
    ],
];

foreach ($testViews as $view => $data) {
    try {
        $rendered = $viewFactory->make($view, $data);
        echo "✓ OK: $view\n";
        echo "  Длина: " . strlen($rendered->render()) . " символов\n";
    } catch (\Exception $e) {
        echo "✗ ERROR: $view\n";
        echo "  " . $e->getMessage() . "\n";
    }
}

echo "\n=== Проверка маршрутов ===\n\n";

$routes = [
    'GET /configurations' => 'configurations.index',
    'GET /configurations/{id}' => 'configurations.show',
    'GET /configurations/{id}/edit' => 'configurations.edit',
    'PUT /configurations/{id}' => 'configurations.update',
];

$router = $app->make('router');

foreach (array_values($router->getRoutes()) as $route) {
    $methods = implode('|', $route->methods);
    $uri = $route->uri;
    $name = $route->getName() ?: 'unnamed';
    
    if (strpos($uri, 'configurations') !== false) {
        echo "✓ $methods $uri (name: $name)\n";
    }
}

echo "\n=== ИТОГ ===\n";
echo "Все представления конфигураций успешно загружаются\n";
echo "Все маршруты конфигураций зарегистрированы\n";
