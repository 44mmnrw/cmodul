<?php
$mysqli = new mysqli('127.0.0.1', 'cmodul', '4bq;=m=)', 'cmodul');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

// Найдём первый кабинет (product_type_id = 1)
$sql = "SELECT id, name, scu FROM details WHERE product_type_id = 1 LIMIT 1";
$result = $mysqli->query($sql);
$cabinet = $result->fetch_assoc();

echo "Первый кабинет:\n";
echo "ID: {$cabinet['id']}\n";
echo "Name: {$cabinet['name']}\n";
echo "SCU: {$cabinet['scu']}\n\n";

// Проверим его компоненты
$sql2 = "SELECT cp.id, cp.cabinet_detail_id, cp.place_detail_id,
                d_place.id as place_id, d_place.name as place_name, d_place.scu as place_scu,
                d_place.product_type_id
         FROM cabinet_place cp
         LEFT JOIN details d_place ON cp.place_detail_id = d_place.id
         WHERE cp.cabinet_detail_id = {$cabinet['id']}
         LIMIT 10";

echo "Компоненты в конфигурации:\n";
echo "============================\n";
$result2 = $mysqli->query($sql2);
if ($result2->num_rows > 0) {
    while ($row = $result2->fetch_assoc()) {
        echo "Component ID: {$row['place_id']}\n";
        echo "  Name: {$row['place_name']}\n";
        echo "  SCU: {$row['place_scu']}\n";
        echo "  Product Type: {$row['product_type_id']}\n";
        echo "\n";
    }
} else {
    echo "Нет компонентов\n";
}

$mysqli->close();
