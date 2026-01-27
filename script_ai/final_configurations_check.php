<?php

echo "=== Финальная проверка миграции конфигураций ===\n\n";

$checks = [
    'Новые файлы представлений' => [
        'resources/views/configurations/index.blade.php',
        'resources/views/configurations/show.blade.php',
        'resources/views/configurations/edit.blade.php',
    ],
    'CSS файлы' => [
        'resources/css/configurations.css',
        'resources/css/configuration-detail.css',
    ],
];

$allPassed = true;

foreach ($checks as $category => $files) {
    echo "📋 $category:\n";
    
    foreach ($files as $file) {
        $fullPath = __DIR__ . '/../' . $file;
        $exists = file_exists($fullPath);
        
        if ($exists) {
            $size = filesize($fullPath);
            echo "  ✓ $file ($size bytes)\n";
        } else {
            echo "  ✗ $file (NOT FOUND)\n";
            $allPassed = false;
        }
    }
    echo "\n";
}

// Проверим маршруты в web.php
echo "📋 Проверка маршрутов в routes/web.php:\n";

$routesFile = __DIR__ . '/../routes/web.php';
$content = file_get_contents($routesFile);

$checks = [
    "view('configurations.index'" => "Маршрут списка конфигураций",
    "view('configurations.show'" => "Маршрут детальной страницы конфигурации",
    "ConfigController::class, 'edit'" => "Контроллер редактирования конфигурации",
];

foreach ($checks as $pattern => $description) {
    if (strpos($content, $pattern) !== false) {
        echo "  ✓ $description\n";
    } else {
        echo "  ✗ $description\n";
        $allPassed = false;
    }
}

echo "\n";

// Проверим контроллер
echo "📋 Проверка контроллера ConfigController:\n";

$controllerFile = __DIR__ . '/../app/Http/Controllers/ConfigController.php';
$content = file_get_contents($controllerFile);

$checks = [
    "view('configurations.edit'" => "Контроллер использует правильное представление edit",
];

foreach ($checks as $pattern => $description) {
    if (strpos($content, $pattern) !== false) {
        echo "  ✓ $description\n";
    } else {
        echo "  ✗ $description\n";
        $allPassed = false;
    }
}

echo "\n";

// Итоговый результат
echo "═══════════════════════════════════════════\n";
if ($allPassed) {
    echo "✅ ВСЕ ПРОВЕРКИ ПРОЙДЕНЫ!\n\n";
    echo "📁 Файловая структура:\n";
    echo "   resources/views/configurations/\n";
    echo "   ├── index.blade.php    (список конфигураций)\n";
    echo "   ├── show.blade.php     (детальная страница)\n";
    echo "   └── edit.blade.php     (форма редактирования)\n\n";
    echo "🎯 Маршруты конфигураций:\n";
    echo "   GET  /configurations         → configurations.index\n";
    echo "   GET  /configurations/{id}    → configurations.show\n";
    echo "   GET  /configurations/{id}/edit → configurations.edit\n";
    echo "   PUT  /configurations/{id}    → configurations.update\n\n";
    echo "✨ Миграция завершена успешно!\n";
} else {
    echo "❌ НЕКОТОРЫЕ ПРОВЕРКИ НЕ ПРОЙДЕНЫ\n";
}
echo "═══════════════════════════════════════════\n";
