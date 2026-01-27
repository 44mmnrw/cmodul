<?php
$mysqli = new mysqli('127.0.0.1', 'cmodul', '4bq;=m=)', 'cmodul');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

// Найдём деталь с place_id = 44.50.00001
$sql = "SELECT id FROM details WHERE scu = '44.50.00001'";
$result = $mysqli->query($sql);
$placeId = $result->fetch_assoc()['id'];

echo "Place ID в details: $placeId\n\n";

// Проверим его конфигурацию
$sql2 = "SELECT cp.id, cp.cabinet_detail_id, cp.place_detail_id,
                d_cab.name as cabinet_name, d_cab.scu as cabinet_scu,
                d_place.name as place_name, d_place.scu as place_scu
         FROM cabinet_place cp
         LEFT JOIN details d_cab ON cp.cabinet_detail_id = d_cab.id
         LEFT JOIN details d_place ON cp.place_detail_id = d_place.id
         WHERE cp.place_detail_id = $placeId
         LIMIT 10";

echo "Конфигурации где это место используется:\n";
echo "============================================\n";
$result2 = $mysqli->query($sql2);
if ($result2->num_rows > 0) {
    while ($row = $result2->fetch_assoc()) {
        echo "ID: {$row['id']}\n";
        echo "  Cabinet: {$row['cabinet_name']} ({$row['cabinet_scu']})\n";
        echo "  Place: {$row['place_name']} ({$row['place_scu']})\n";
        echo "\n";
    }
} else {
    echo "Нет результатов\n";
}

// Проверим противоположное - места входящие в кабинет
$sql3 = "SELECT cp.id, cp.cabinet_detail_id, cp.place_detail_id,
                d_cab.name as cabinet_name, d_cab.scu as cabinet_scu,
                d_place.name as place_name, d_place.scu as place_scu
         FROM cabinet_place cp
         LEFT JOIN details d_cab ON cp.cabinet_detail_id = d_cab.id
         LEFT JOIN details d_place ON cp.place_detail_id = d_place.id
         WHERE cp.cabinet_detail_id = $placeId
         LIMIT 10";

echo "\n\nМеста входящие в конфигурацию этого кабинета:\n";
echo "============================================\n";
$result3 = $mysqli->query($sql3);
if ($result3->num_rows > 0) {
    while ($row = $result3->fetch_assoc()) {
        echo "ID: {$row['id']}\n";
        echo "  Cabinet: {$row['cabinet_name']} ({$row['cabinet_scu']})\n";
        echo "  Place: {$row['place_name']} ({$row['place_scu']})\n";
        echo "\n";
    }
} else {
    echo "Нет результатов\n";
}

$mysqli->close();
