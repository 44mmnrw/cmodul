<?php
$db = new PDO('sqlite:database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function getTableSchema($db, $table) {
    $stmt = $db->prepare("PRAGMA table_info($table)");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo "╔════════════════════════════════════════════════════════╗\n";
echo "║          СТРУКТУРА ТАБЛИЦ В БД                        ║\n";
echo "╚════════════════════════════════════════════════════════╝\n\n";

echo "📋 ТАБЛИЦА: cabinet_place (ЭТАЛОН)\n";
echo "─────────────────────────────────────────\n";
$cols = getTableSchema($db, 'cabinet_place');
foreach ($cols as $col) {
    $pk = $col['pk'] ? ' [PK]' : '';
    $fk = '';
    echo sprintf("  %-20s %-15s NN:%-3s%s\n", 
        $col['name'],
        $col['type'],
        $col['notnull'] ? 'YES' : 'NO',
        $pk
    );
}

echo "\n📋 ТАБЛИЦА: place_detail (НУЖНО ПЕРЕДЕЛАТЬ)\n";
echo "─────────────────────────────────────────\n";
$cols = getTableSchema($db, 'place_detail');
foreach ($cols as $col) {
    $pk = $col['pk'] ? ' [PK]' : '';
    echo sprintf("  %-20s %-15s NN:%-3s%s\n", 
        $col['name'],
        $col['type'],
        $col['notnull'] ? 'YES' : 'NO',
        $pk
    );
}

echo "\n📋 ТАБЛИЦА: details (ЦЕЛЕВАЯ)\n";
echo "─────────────────────────────────────────\n";
$cols = getTableSchema($db, 'details');
foreach ($cols as $col) {
    $pk = $col['pk'] ? ' [PK]' : '';
    echo sprintf("  %-20s %-15s NN:%-3s%s\n", 
        $col['name'],
        $col['type'],
        $col['notnull'] ? 'YES' : 'NO',
        $pk
    );
}

// Проверим индексы и FK
echo "\n🔗 ВНЕШНИЕ КЛЮЧИ И ИНДЕКСЫ:\n";
echo "─────────────────────────────────────────\n";

// Для SQLite проверяем через информационную схему
echo "\nTablesplaces_detail:\n";
$stmt = $db->prepare("PRAGMA foreign_key_list(place_detail)");
$stmt->execute();
$fks = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($fks) {
    foreach ($fks as $fk) {
        echo "  FK: {$fk['from']} -> {$fk['table']}.{$fk['to']}\n";
    }
} else {
    echo "  Нет внешних ключей\n";
}

$db = null;
?>
