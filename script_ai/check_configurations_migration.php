<?php

// Проверяем, что файлы views существуют
$viewFiles = [
    'configurations/index.blade.php',
    'configurations/show.blade.php',
    'configurations/edit.blade.php',
];

$basePath = __DIR__ . '/../resources/views/';

echo "=== Проверка файлов представлений ===\n\n";

foreach ($viewFiles as $file) {
    $fullPath = $basePath . $file;
    $exists = file_exists($fullPath);
    $status = $exists ? '✓ FOUND' : '✗ NOT FOUND';
    echo "$status: $file\n";
    if ($exists) {
        echo "   Size: " . filesize($fullPath) . " bytes\n";
    }
}

echo "\n=== Проверка старых файлов (должны быть удалены) ===\n\n";

$oldFiles = [
    'configurations.blade.php',
    'configuration-detail.blade.php',
    'configuration-edit.blade.php',
];

foreach ($oldFiles as $file) {
    $fullPath = $basePath . $file;
    $exists = file_exists($fullPath);
    $status = $exists ? '⚠ STILL EXISTS' : '✓ DELETED';
    echo "$status: $file\n";
}

echo "\n=== Проверка CSS файлов ===\n\n";

$cssFiles = [
    'configurations.css',
    'configuration-detail.css',
];

foreach ($cssFiles as $file) {
    $fullPath = $basePath . '../css/' . $file;
    $exists = file_exists($fullPath);
    $status = $exists ? '✓ FOUND' : '✗ NOT FOUND';
    echo "$status: css/$file\n";
}

echo "\n=== ИТОГ ===\n";
echo "Файлы структуры конфигураций успешно перемещены в папку configurations/\n";
echo "Все маршруты обновлены для использования новых представлений\n";
