<?php
$mysqli = new mysqli('127.0.0.1', 'cmodul', '4bq;=m=)', 'cmodul');

if ($mysqli->connect_error) {
    die('Ошибка: ' . $mysqli->connect_error);
}

echo "Откатываю изменения...\n\n";

// Удаляем колонку quantity из cabinet_place
echo "1. Удаляю колонку quantity из cabinet_place...\n";
$sql1 = "ALTER TABLE cabinet_place DROP COLUMN quantity";
if ($mysqli->query($sql1)) {
    echo "   ✓ Готово\n";
} else {
    if (strpos($mysqli->error, "check that column/key exists") !== false) {
        echo "   ✓ Колонки нет, пропускаю\n";
    } else {
        echo "   Ошибка: " . $mysqli->error . "\n";
    }
}

// Очищаем cabinet_place полностью
echo "\n2. Очищаю таблицу cabinet_place...\n";
if ($mysqli->query("DELETE FROM cabinet_place")) {
    echo "   ✓ Готово\n";
} else {
    echo "   Ошибка: " . $mysqli->error . "\n";
}

// Отмечаем миграцию как не выполненную
echo "\n3. Отмечаю миграцию как откачена...\n";
$sql3 = "DELETE FROM migrations WHERE migration = '2026_01_27_000001_add_quantity_to_cabinet_place'";
if ($mysqli->query($sql3)) {
    echo "   ✓ Готово\n";
} else {
    echo "   Ошибка: " . $mysqli->error . "\n";
}

echo "\n✓ Все откачено. Готово к заново заливке.\n";

$mysqli->close();
?>
