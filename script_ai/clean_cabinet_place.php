<?php
$mysqli = new mysqli('127.0.0.1', 'cmodul', '4bq;=m=)', 'cmodul');

if ($mysqli->connect_error) {
    die('Ошибка: ' . $mysqli->connect_error);
}

echo "Очищаю таблицу cabinet_place и сбрасываю счетчик...\n";

// Удаляем все данные
$sql1 = "TRUNCATE TABLE cabinet_place";
if ($mysqli->query($sql1)) {
    echo "✓ Таблица очищена и счетчик сброшен\n";
} else {
    echo "Ошибка: " . $mysqli->error . "\n";
}

// Проверяем результат
$result = $mysqli->query("SELECT COUNT(*) as cnt FROM cabinet_place");
$row = $result->fetch_assoc();
echo "✓ Записей в cabinet_place: {$row['cnt']}\n";

$mysqli->close();
?>
