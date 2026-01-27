<?php
$mysqli = new mysqli('127.0.0.1', 'cmodul', '4bq;=m=)', 'cmodul');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

// Вставить данные из cabinets в details
$sql = "INSERT INTO details (name, scu, product_type_id, created_at, updated_at)
        SELECT name, cabinet_id, 1, NOW(), NOW()
        FROM cabinets
        ON DUPLICATE KEY UPDATE scu = VALUES(scu), product_type_id = 1";

if ($mysqli->query($sql)) {
    echo "✓ Вставлено/обновлено записей: " . $mysqli->affected_rows . "\n";
} else {
    echo "Ошибка: " . $mysqli->error . "\n";
}

$mysqli->close();
