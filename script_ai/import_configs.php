<?php
$mysqli = new mysqli('127.0.0.1', 'cmodul', '4bq;=m=)', 'cmodul');

if ($mysqli->connect_error) {
    die('Ошибка подключения: ' . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");

$csvFile = 'data/configs.csv';

if (!file_exists($csvFile)) {
    die("Файл $csvFile не найден\n");
}

echo "╔════════════════════════════════════════════════════════╗\n";
echo "║          ИМПОРТ ДАННЫХ В ТАБЛИЦУ configs              ║\n";
echo "╚════════════════════════════════════════════════════════╝\n\n";

$file = fopen($csvFile, 'r');
if (!$file) {
    die("Не удалось открыть файл $csvFile\n");
}

// Читаем заголовок
$header = fgetcsv($file, 0, ';');
echo "Заголовок: " . implode(', ', $header) . "\n\n";

$inserted = 0;
$skipped = 0;
$errors = [];

while (($data = fgetcsv($file, 0, ';')) !== false) {
    $master_scu = trim($data[0], '"');
    $slave_scu = trim($data[1], '"');
    $qty = intval(trim($data[2], '"'));

    // Находим master_id по scu
    $stmt = $mysqli->prepare("SELECT id FROM details WHERE scu = ? LIMIT 1");
    $stmt->bind_param("s", $master_scu);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $skipped++;
        $errors[] = "Master SCU '$master_scu' не найден";
        $stmt->close();
        continue;
    }
    
    $master_row = $result->fetch_assoc();
    $master_id = $master_row['id'];
    $stmt->close();

    // Находим slave_id по scu
    $stmt = $mysqli->prepare("SELECT id FROM details WHERE scu = ? LIMIT 1");
    $stmt->bind_param("s", $slave_scu);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $skipped++;
        $errors[] = "Slave SCU '$slave_scu' не найден";
        $stmt->close();
        continue;
    }
    
    $slave_row = $result->fetch_assoc();
    $slave_id = $slave_row['id'];
    $stmt->close();

    // Вставляем запись в configs
    $stmt = $mysqli->prepare(
        "INSERT INTO configs (master_id, slave_id, quantity, created_at, updated_at) 
         VALUES (?, ?, ?, NOW(), NOW())"
    );
    $stmt->bind_param("iii", $master_id, $slave_id, $qty);
    
    if ($stmt->execute()) {
        $inserted++;
    } else {
        $skipped++;
        $errors[] = "Ошибка вставки: master_id=$master_id, slave_id=$slave_id - " . $stmt->error;
    }
    
    $stmt->close();
}

fclose($file);

echo "\n✓ Импорт завершен\n";
echo "  Вставлено: $inserted записей\n";
echo "  Пропущено: $skipped записей\n";

if (!empty($errors)) {
    echo "\n⚠️ Ошибки (первые 10):\n";
    for ($i = 0; $i < min(10, count($errors)); $i++) {
        echo "  - " . $errors[$i] . "\n";
    }
}

// Проверяем финальное количество
$result = $mysqli->query("SELECT COUNT(*) as cnt FROM configs");
$row = $result->fetch_assoc();
echo "\nВсего записей в configs: {$row['cnt']}\n";

$mysqli->close();
?>
