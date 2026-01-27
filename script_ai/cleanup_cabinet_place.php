<?php
$mysqli = new mysqli('127.0.0.1', 'cmodul', '4bq;=m=)', 'cmodul');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

echo "Удаляем старые внешние ключи из cabinet_place...\n";

// Удалить внешние ключи
$sql1 = "ALTER TABLE cabinet_place 
         DROP FOREIGN KEY cabinet_place_cabinet_id_foreign";

if ($mysqli->query($sql1)) {
    echo "✓ Внешний ключ cabinet_place_cabinet_id_foreign удален\n";
} else {
    echo "⚠ Ошибка при удалении: " . $mysqli->error . "\n";
}

$sql2 = "ALTER TABLE cabinet_place 
         DROP FOREIGN KEY cabinet_place_place_id_foreign";

if ($mysqli->query($sql2)) {
    echo "✓ Внешний ключ cabinet_place_place_id_foreign удален\n";
} else {
    echo "⚠ Ошибка при удалении: " . $mysqli->error . "\n";
}

echo "\nУдаляем индексы на старые колонки...\n";

// Удалить индексы
$sql3 = "ALTER TABLE cabinet_place 
         DROP KEY cabinet_place_cabinet_id_foreign";

if ($mysqli->query($sql3)) {
    echo "✓ Индекс cabinet_place_cabinet_id_foreign удален\n";
} else {
    if (strpos($mysqli->error, 'Cant DROP') !== false || strpos($mysqli->error, 'doesn\'t exist') !== false) {
        echo "✓ Индекс уже удален или не существует\n";
    } else {
        echo "⚠ Ошибка: " . $mysqli->error . "\n";
    }
}

$sql4 = "ALTER TABLE cabinet_place 
         DROP KEY cabinet_place_place_id_foreign";

if ($mysqli->query($sql4)) {
    echo "✓ Индекс cabinet_place_place_id_foreign удален\n";
} else {
    if (strpos($mysqli->error, 'Cant DROP') !== false || strpos($mysqli->error, 'doesn\'t exist') !== false) {
        echo "✓ Индекс уже удален или не существует\n";
    } else {
        echo "⚠ Ошибка: " . $mysqli->error . "\n";
    }
}

echo "\nУдаляем старые колонки...\n";

// Удалить колонки
$sql5 = "ALTER TABLE cabinet_place 
         DROP COLUMN cabinet_id,
         DROP COLUMN place_id";

if ($mysqli->query($sql5)) {
    echo "✓ Колонки cabinet_id и place_id удалены\n";
} else {
    echo "⚠ Ошибка при удалении: " . $mysqli->error . "\n";
}

echo "\n✓ ВСЁ ГОТОВО!\n";

$mysqli->close();
