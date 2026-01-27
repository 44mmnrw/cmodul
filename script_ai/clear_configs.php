<?php
$mysqli = new mysqli('127.0.0.1', 'cmodul', '4bq;=m=)', 'cmodul');

echo "Очищаю таблицу configs...\n";
$mysqli->query('DELETE FROM configs');

$result = $mysqli->query('SELECT COUNT(*) as cnt FROM configs');
$row = $result->fetch_assoc();
echo "✓ Таблица очищена. Записей: {$row['cnt']}\n";

$mysqli->close();
?>
