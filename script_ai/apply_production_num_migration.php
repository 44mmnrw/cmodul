<?php

require __DIR__ . '/../bootstrap/app.php';

use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;

try {
    $app = app();
    $migrator = new Migrator(
        $app->make('migration.repository'),
        $app->make('db'),
        new Filesystem()
    );

    $migrator->setConnection('mysql');
    
    // Получить все файлы миграций
    $files = $migrator->getMigrationFiles(__DIR__ . '/../../database/migrations');
    
    // Найти и применить новую миграцию
    $targetMigration = '2026_01_28_160000_add_production_num_to_production_orders';
    
    if (array_key_exists($targetMigration, $files)) {
        echo "🔄 Применяю миграцию: $targetMigration\n";
        
        // Загрузить и выполнить миграцию
        $path = $files[$targetMigration];
        $migration = require $path;
        $migration->up();
        
        // Записать в таблицу migrations
        $migrator->getRepository()->log($targetMigration, 1);
        
        echo "✅ Миграция успешно применена!\n";
    } else {
        echo "❌ Миграция не найдена\n";
    }

} catch (Exception $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
    exit(1);
}
