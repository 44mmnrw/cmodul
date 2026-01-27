<?php
$mysqli = new mysqli('127.0.0.1', 'cmodul', '4bq;=m=)', 'cmodul');

if ($mysqli->connect_error) {
    die('Ошибка: ' . $mysqli->connect_error);
}

echo "Проверка дубликатов в cabinet_place...\n";

// Проверяем дубликаты
$result = $mysqli->query("
    SELECT place_detail_id, cabinet_detail_id, COUNT(*) as cnt
    FROM cabinet_place
    GROUP BY place_detail_id, cabinet_detail_id
    HAVING cnt > 1
");

if ($result->num_rows > 0) {
    echo "❌ Найдены дубликаты:\n";
    while ($row = $result->fetch_assoc()) {
        echo "  place_detail_id={$row['place_detail_id']}, cabinet_detail_id={$row['cabinet_detail_id']}: {$row['cnt']} записей\n";
    }
    
    // Удаляем дубликаты, оставляя только первую запись
    echo "\nУдаляю дубликаты...\n";
    $sql = "
        DELETE FROM cabinet_place
        WHERE id NOT IN (
            SELECT MIN(id) FROM (
                SELECT MIN(id) as id
                FROM cabinet_place
                GROUP BY place_detail_id, cabinet_detail_id
            ) t
        )
    ";
    
    if ($mysqli->query($sql)) {
        echo "✓ Удалено дубликатов\n";
    } else {
        echo "Ошибка: " . $mysqli->error . "\n";
    }
} else {
    echo "✓ Дубликатов не найдено\n";
}

// Показываем финальное количество
$result = $mysqli->query("SELECT COUNT(*) as cnt FROM cabinet_place");
$row = $result->fetch_assoc();
echo "\nВсего записей в cabinet_place: {$row['cnt']}\n";

$mysqli->close();
?>
