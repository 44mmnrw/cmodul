<?php
$mysqli = new mysqli('127.0.0.1', 'cmodul', '4bq;=m=)', 'cmodul');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

echo "Шаг 1: Добавляем новые колонки в cabinet_place\n";

// Добавить колонки для id
$sql = "ALTER TABLE cabinet_place 
        ADD COLUMN cabinet_detail_id BIGINT UNSIGNED NULL AFTER id,
        ADD COLUMN place_detail_id BIGINT UNSIGNED NULL AFTER cabinet_detail_id";

if ($mysqli->query($sql)) {
    echo "✓ Колонки добавлены\n";
} else {
    if (strpos($mysqli->error, 'Duplicate column') !== false) {
        echo "✓ Колонки уже существуют\n";
    } else {
        echo "Ошибка: " . $mysqli->error . "\n";
    }
}

echo "\nШаг 2: Заполняем cabinet_detail_id и place_detail_id\n";

// Обновляем cabinet_detail_id - ищем в details где scu = cabinet_id и product_type_id = 1
$sql2 = "UPDATE cabinet_place cp
         JOIN details d_cabinet ON d_cabinet.scu = cp.cabinet_id AND d_cabinet.product_type_id = 1
         SET cp.cabinet_detail_id = d_cabinet.id";

if ($mysqli->query($sql2)) {
    echo "✓ cabinet_detail_id заполнен: " . $mysqli->affected_rows . " записей\n";
} else {
    echo "Ошибка: " . $mysqli->error . "\n";
}

// Обновляем place_detail_id - ищем в details где scu = place_id и product_type_id = 2
$sql3 = "UPDATE cabinet_place cp
         JOIN details d_place ON d_place.scu = cp.place_id AND d_place.product_type_id = 2
         SET cp.place_detail_id = d_place.id";

if ($mysqli->query($sql3)) {
    echo "✓ place_detail_id заполнен: " . $mysqli->affected_rows . " записей\n";
} else {
    echo "Ошибка: " . $mysqli->error . "\n";
}

echo "\nШаг 3: Добавляем внешние ключи\n";

// Добавить внешние ключи
$sql4 = "ALTER TABLE cabinet_place 
         ADD CONSTRAINT cabinet_place_cabinet_detail_id_foreign 
         FOREIGN KEY (cabinet_detail_id) REFERENCES details(id) ON DELETE CASCADE";

if ($mysqli->query($sql4)) {
    echo "✓ Внешний ключ для cabinet_detail_id добавлен\n";
} else {
    if (strpos($mysqli->error, 'Duplicate key') !== false) {
        echo "✓ Внешний ключ для cabinet_detail_id уже существует\n";
    } else {
        echo "Ошибка: " . $mysqli->error . "\n";
    }
}

$sql5 = "ALTER TABLE cabinet_place 
         ADD CONSTRAINT cabinet_place_place_detail_id_foreign 
         FOREIGN KEY (place_detail_id) REFERENCES details(id) ON DELETE CASCADE";

if ($mysqli->query($sql5)) {
    echo "✓ Внешний ключ для place_detail_id добавлен\n";
} else {
    if (strpos($mysqli->error, 'Duplicate key') !== false) {
        echo "✓ Внешний ключ для place_detail_id уже существует\n";
    } else {
        echo "Ошибка: " . $mysqli->error . "\n";
    }
}

echo "\n✓ ВСЁ ГОТОВО!\n";

$mysqli->close();
