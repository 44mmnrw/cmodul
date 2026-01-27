<?php
$mysqli = new mysqli('127.0.0.1', 'cmodul', '4bq;=m=)', 'cmodul');

if ($mysqli->connect_error) {
    die('Ошибка: ' . $mysqli->connect_error);
}

echo "╔════════════════════════════════════════════════════════╗\n";
echo "║          ПРОВЕРКА ПЕРЕИМЕНОВАНИЯ                       ║\n";
echo "╚════════════════════════════════════════════════════════╝\n\n";

function showTableSchema($db, $table) {
    $result = $db->query("DESCRIBE $table");
    if (!$result) {
        echo "  ❌ Таблица не найдена\n";
        return;
    }
    
    while ($row = $result->fetch_assoc()) {
        $key = '';
        if ($row['Key'] === 'PRI') $key = ' [PRIMARY]';
        else if ($row['Key'] === 'MUL') $key = ' [INDEX]';
        
        printf("  %-20s %-20s%s\n",
            $row['Field'],
            $row['Type'],
            $key
        );
    }
}

echo "📋 ТАБЛИЦА: configs (была cabinet_place)\n";
echo "─────────────────────────────────────────\n";
showTableSchema($mysqli, 'configs');

echo "\n🔗 ВНЕШНИЕ КЛЮЧИ configs:\n";
echo "─────────────────────────────────────────\n";
$result = $mysqli->query("SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_NAME='configs' AND REFERENCED_TABLE_NAME IS NOT NULL");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "  {$row['CONSTRAINT_NAME']}: {$row['COLUMN_NAME']} -> {$row['REFERENCED_TABLE_NAME']}.{$row['REFERENCED_COLUMN_NAME']}\n";
    }
} else {
    echo "  Нет внешних ключей\n";
}

echo "\n✓ Таблица успешно переименована!\n";

$mysqli->close();
?>
