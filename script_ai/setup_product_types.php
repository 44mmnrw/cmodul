<?php

require __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\DB;

DB::statement('
    CREATE TABLE IF NOT EXISTS product_types (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL UNIQUE,
        description VARCHAR(255) NULL,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
');

DB::statement('
    ALTER TABLE details 
    ADD COLUMN product_type_id BIGINT UNSIGNED NULL AFTER source_id,
    ADD CONSTRAINT details_product_type_id_foreign 
    FOREIGN KEY (product_type_id) REFERENCES product_types(id) ON DELETE SET NULL
');

// Вставить типы
DB::table('product_types')->insertOrIgnore([
    ['name' => 'Готовое изделие', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Комплектующие', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Детали', 'created_at' => now(), 'updated_at' => now()],
]);

echo "✓ Таблица product_types создана\n";
echo "✓ Колонка product_type_id добавлена в details\n";
echo "✓ Типы продуктов добавлены\n";
