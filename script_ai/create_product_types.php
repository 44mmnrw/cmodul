<?php
// Простой скрипт для создания таблицы и колонки
$mysqli = new mysqli('127.0.0.1', 'cmodul', '4bq;=m=)', 'cmodul');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

// Создать таблицу product_types
$sql1 = "CREATE TABLE IF NOT EXISTS product_types (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($mysqli->query($sql1)) {
    echo "✓ Таблица product_types создана\n";
} else {
    echo "Ошибка: " . $mysqli->error . "\n";
}

// Добавить значения
$values = ['Готовое изделие', 'Комплектующие', 'Детали'];
foreach ($values as $val) {
    $sql = "INSERT IGNORE INTO product_types (name) VALUES ('" . $mysqli->real_escape_string($val) . "')";
    $mysqli->query($sql);
}
echo "✓ Значения добавлены\n";

// Добавить колонку в details
$sql2 = "ALTER TABLE details ADD COLUMN product_type_id BIGINT UNSIGNED NULL";
if ($mysqli->query($sql2)) {
    echo "✓ Колонка product_type_id добавлена\n";
} else {
    if (strpos($mysqli->error, 'Duplicate column') !== false) {
        echo "✓ Колонка product_type_id уже существует\n";
    } else {
        echo "Ошибка: " . $mysqli->error . "\n";
    }
}

// Добавить внешний ключ
$sql3 = "ALTER TABLE details ADD CONSTRAINT details_product_type_id_foreign FOREIGN KEY (product_type_id) REFERENCES product_types(id) ON DELETE SET NULL";
if ($mysqli->query($sql3)) {
    echo "✓ Внешний ключ добавлен\n";
} else {
    if (strpos($mysqli->error, 'Duplicate key') !== false) {
        echo "✓ Внешний ключ уже существует\n";
    } else {
        echo "Ошибка: " . $mysqli->error . "\n";
    }
}

$mysqli->close();
echo "\n✓ ВСЁ ГОТОВО!\n";
