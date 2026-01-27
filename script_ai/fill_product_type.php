<?php
$mysqli = new mysqli('127.0.0.1', 'cmodul', '4bq;=m=)', 'cmodul');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

$sql = "UPDATE details SET product_type_id = 3";
if ($mysqli->query($sql)) {
    echo "✓ Обновлено записей: " . $mysqli->affected_rows . "\n";
} else {
    echo "Ошибка: " . $mysqli->error . "\n";
}

$mysqli->close();
