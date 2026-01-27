<?php
$db = new mysqli('127.0.0.1', 'cmodul', '4bq;=m=)', 'cmodul');

if ($db->connect_error) {
    die('Ошибка подключения: ' . $db->connect_error);
}

echo "╔════════════════════════════════════════════════════════╗\n";
echo "║          СТРУКТУРА ТАБЛИЦ В MySQL                     ║\n";
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
        else if ($row['Key'] === 'UNI') $key = ' [UNIQUE]';
        
        printf("  %-20s %-20s %-3s%s\n",
            $row['Field'],
            $row['Type'],
            $row['Null'] === 'YES' ? 'YES' : 'NO',
            $key
        );
    }
}

echo "📋 ТАБЛИЦА: cabinet_place (ЭТАЛОН)\n";
echo "─────────────────────────────────────────\n";
showTableSchema($db, 'cabinet_place');

echo "\n📋 ТАБЛИЦА: place_detail (НУЖНО ПЕРЕДЕЛАТЬ)\n";
echo "─────────────────────────────────────────\n";
showTableSchema($db, 'place_detail');

echo "\n📋 ТАБЛИЦА: details (ЦЕЛЕВАЯ)\n";
echo "─────────────────────────────────────────\n";
showTableSchema($db, 'details');

// Проверим внешние ключи
echo "\n🔗 ВНЕШНИЕ КЛЮЧИ cabinet_place:\n";
echo "─────────────────────────────────────────\n";
$result = $db->query("SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_NAME='cabinet_place' AND REFERENCED_TABLE_NAME IS NOT NULL");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "  {$row['CONSTRAINT_NAME']}: {$row['COLUMN_NAME']} -> {$row['REFERENCED_TABLE_NAME']}.{$row['REFERENCED_COLUMN_NAME']}\n";
    }
} else {
    echo "  Нет внешних ключей\n";
}

echo "\n🔗 ВНЕШНИЕ КЛЮЧИ place_detail:\n";
echo "─────────────────────────────────────────\n";
$result = $db->query("SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_NAME='place_detail' AND REFERENCED_TABLE_NAME IS NOT NULL");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "  {$row['CONSTRAINT_NAME']}: {$row['COLUMN_NAME']} -> {$row['REFERENCED_TABLE_NAME']}.{$row['REFERENCED_COLUMN_NAME']}\n";
    }
} else {
    echo "  Нет внешних ключей\n";
}

$db->close();
?>
