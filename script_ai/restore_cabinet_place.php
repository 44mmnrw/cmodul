<?php
$mysqli = new mysqli('127.0.0.1', 'cmodul', '4bq;=m=)', 'cmodul');

if ($mysqli->connect_error) {
    die('Ошибка: ' . $mysqli->connect_error);
}

echo "Восстанавливаю cabinet_place из place_detail...\n";

// Первый удаляем ВСЕ текущие записи
$mysqli->query("DELETE FROM cabinet_place");
echo "✓ Таблица очищена\n";

// Теперь вставляем корректные данные
$sql = "
    INSERT INTO cabinet_place (place_detail_id, cabinet_detail_id, quantity, created_at, updated_at)
    SELECT 
        d_place.id as place_detail_id,
        pd.detail_id as cabinet_detail_id,
        pd.quantity,
        pd.created_at,
        pd.updated_at
    FROM place_detail pd
    JOIN details d_place ON d_place.scu = pd.place_id
";

if ($mysqli->query($sql)) {
    $result = $mysqli->query("SELECT COUNT(*) as cnt FROM cabinet_place");
    $row = $result->fetch_assoc();
    echo "✓ Данные перенесены: {$row['cnt']} записей\n";
} else {
    echo "Ошибка: " . $mysqli->error . "\n";
}

$mysqli->close();
?>
