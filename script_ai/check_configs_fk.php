<?php
$mysqli = new mysqli('127.0.0.1', 'cmodul', '4bq;=m=)', 'cmodul');

$result = $mysqli->query("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_NAME='configs' AND REFERENCED_TABLE_NAME IS NOT NULL");

echo "Внешние ключи в configs:\n";
while ($row = $result->fetch_assoc()) {
    echo "- " . $row['CONSTRAINT_NAME'] . "\n";
}

$mysqli->close();
?>
