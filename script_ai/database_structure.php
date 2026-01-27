<?php
/**
 * Database Structure Analyzer - ТОЛЬКО БАЗА CMODUL
 * Полный анализ структуры базы данных
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

class DatabaseAnalyzer
{
    protected $database = 'cmodul';
    protected $tables = [];

    public function run()
    {
        echo "\n" . str_repeat("=", 120) . "\n";
        echo "АНАЛИЗ СТРУКТУРЫ БАЗЫ ДАННЫХ: {$this->database}\n";
        echo str_repeat("=", 120) . "\n\n";

        $this->getTables();
        $this->analyzeTables();
        $this->analyzeForeignKeys();
        
        echo "\n" . str_repeat("=", 120) . "\n";
        echo "✅ АНАЛИЗ ЗАВЕРШЕН\n";
        echo str_repeat("=", 120) . "\n\n";
    }

    protected function getTables()
    {
        $tables = DB::select("
            SELECT TABLE_NAME 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = '{$this->database}'
            ORDER BY TABLE_NAME
        ");

        $this->tables = array_map(fn($t) => $t->TABLE_NAME, $tables);
        echo "📊 ВСЕГО ТАБЛИЦ: " . count($this->tables) . "\n\n";
    }

    protected function analyzeTables()
    {
        echo str_repeat("-", 120) . "\n";
        echo "ПОЛНАЯ СТРУКТУРА ТАБЛИЦ\n";
        echo str_repeat("-", 120) . "\n\n";

        foreach ($this->tables as $table) {
            $this->printTable($table);
        }
    }

    protected function printTable($table)
    {
        echo "📋 ТАБЛИЦА: {$table}\n";
        echo str_repeat("-", 100) . "\n";

        // Статистика таблицы
        $stats = DB::select("
            SELECT 
                ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) as size_mb
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = '{$this->database}' AND TABLE_NAME = '{$table}'
        ");

        $rows = DB::table($table)->count();
        echo "  Размер: {$stats[0]->size_mb} MB | Строк: {$rows}\n\n";

        // Столбцы
        echo "  СТОЛБЦЫ:\n";
        $columns = DB::select("
            SELECT 
                COLUMN_NAME,
                DATA_TYPE,
                IS_NULLABLE,
                COLUMN_KEY,
                EXTRA,
                COLUMN_DEFAULT
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = '{$this->database}' AND TABLE_NAME = '{$table}'
            ORDER BY ORDINAL_POSITION
        ");

        foreach ($columns as $col) {
            $type = $col->DATA_TYPE;
            $null = $col->IS_NULLABLE === 'YES' ? 'NULL' : 'NOT NULL';
            $key = $col->COLUMN_KEY ? "[{$col->COLUMN_KEY}]" : '';
            $extra = $col->EXTRA ? "{$col->EXTRA}" : '';
            $default = $col->COLUMN_DEFAULT ? "DEFAULT: {$col->COLUMN_DEFAULT}" : '';

            printf("    %-30s %-20s %-12s %-8s %-20s %s\n",
                $col->COLUMN_NAME,
                $type,
                $null,
                $key,
                $extra,
                $default
            );
        }

        // Индексы
        echo "\n  ИНДЕКСЫ:\n";
        $indexes = DB::select("
            SELECT 
                INDEX_NAME,
                GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) as columns,
                NON_UNIQUE
            FROM INFORMATION_SCHEMA.STATISTICS 
            WHERE TABLE_SCHEMA = '{$this->database}' AND TABLE_NAME = '{$table}'
            GROUP BY INDEX_NAME, NON_UNIQUE
        ");

        foreach ($indexes as $idx) {
            $unique = $idx->NON_UNIQUE ? 'Regular' : 'UNIQUE';
            echo "    {$idx->INDEX_NAME} ({$unique}): {$idx->columns}\n";
        }

        echo "\n";
    }

    protected function analyzeForeignKeys()
    {
        echo str_repeat("-", 100) . "\n";
        echo "FOREIGN KEY RELATIONSHIPS\n";
        echo str_repeat("-", 100) . "\n\n";

        $foreignKeys = DB::select("SELECT 
            CONSTRAINT_NAME,
            TABLE_NAME,
            COLUMN_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? AND REFERENCED_TABLE_NAME IS NOT NULL", [$this->database]);

        if (empty($foreignKeys)) {
            echo "No foreign keys found\n\n";
            return;
        }

        foreach ($foreignKeys as $fk) {
            echo "🔗 {$fk->TABLE_NAME}.{$fk->COLUMN_NAME}\n";
            echo "   → {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
            echo "   Constraint: {$fk->CONSTRAINT_NAME}\n\n";
        }
    }

    protected function analyzeRelationships()
    {
        echo str_repeat("-", 100) . "\n";
        echo "TABLE RELATIONSHIPS DIAGRAM\n";
        echo str_repeat("-", 100) . "\n\n";

        $relationships = DB::select("SELECT 
            TABLE_NAME,
            COLUMN_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? AND REFERENCED_TABLE_NAME IS NOT NULL
            ORDER BY TABLE_NAME", [$this->database]);

        if (empty($relationships)) {
            echo "No relationships found\n\n";
            return;
        }

        foreach ($this->tables as $table) {
            $rels = array_filter($relationships, fn($r) => $r->TABLE_NAME === $table);
            if (!empty($rels)) {
                echo "{$table}\n";
                foreach ($rels as $rel) {
                    echo "  └── {$rel->COLUMN_NAME} → {$rel->REFERENCED_TABLE_NAME}.{$rel->REFERENCED_COLUMN_NAME}\n";
                }
                echo "\n";
            }
        }
    }
}

// Запуск анализа
try {
    $analyzer = new DatabaseAnalyzer();
    $analyzer->run();
} catch (Exception $e) {
    echo "❌ ОШИБКА: " . $e->getMessage() . "\n";
    exit(1);
}
