<?php
$mysqli = new mysqli('127.0.0.1', 'cmodul', '4bq;=m=)', 'cmodul');

if ($mysqli->connect_error) {
    die('Ошибка: ' . $mysqli->connect_error);
}

echo "Удаляю quantity из cabinet_place...\n";

// Удаляем данные которые были добавлены из place_detail
$sql1 = "DELETE FROM cabinet_place WHERE created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
if ($mysqli->query($sql1)) {
    echo "✓ Данные удалены\n";
} else {
    echo "Ошибка: " . $mysqli->error . "\n";
}

// Удаляем колонку
$sql2 = "ALTER TABLE cabinet_place DROP COLUMN quantity";
if ($mysqli->query($sql2)) {
    echo "✓ Колонка quantity удалена\n";
} else {
    echo "Ошибка: " . $mysqli->error . "\n";
}

// Отмечаем миграцию как невыполненную
$sql3 = "DELETE FROM migrations WHERE migration = '2026_01_27_000001_add_quantity_to_cabinet_place'";
if ($mysqli->query($sql3)) {
    echo "✓ Миграция отмечена как откачена\n";
} else {
    echo "Ошибка: " . $mysqli->error . "\n";
}

$mysqli->close();
?>
