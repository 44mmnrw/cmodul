<?php
$db = new PDO(
    'mysql:host=127.0.0.1;dbname=cmodul',
    'cmodul',
    '4bq;=m=)'
);

echo "=== Checking latest migrations ===\n";
$stmt = $db->query('SELECT migration, batch FROM migrations ORDER BY batch DESC LIMIT 5');
$migrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($migrations as $m) {
    echo $m['migration'] . ' (batch: ' . $m['batch'] . ')' . "\n";
}

echo "\n=== Adding planned_date column to production_orders ===\n";
try {
    $db->exec("ALTER TABLE production_orders ADD COLUMN planned_date DATE NULL COMMENT 'Плановая дата изготовления' AFTER status");
    echo "✓ Column planned_date added successfully\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Recording migration in migrations table ===\n";
try {
    $maxBatch = $db->query('SELECT MAX(batch) as max_batch FROM migrations')->fetch(PDO::FETCH_ASSOC);
    $nextBatch = ($maxBatch['max_batch'] ?? 0) + 1;
    $db->exec("INSERT INTO migrations (migration, batch) VALUES ('2026_01_28_150000_add_planned_date_to_production_orders', $nextBatch)");
    echo "✓ Migration recorded\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate') !== false) {
        echo "Already recorded\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Done! ===\n";
