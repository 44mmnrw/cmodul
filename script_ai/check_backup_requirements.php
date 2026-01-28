<?php

/**
 * Скрипт для проверки доступности MySQL инструментов и подключения к БД
 * 
 * Проверяет:
 * - Наличие mysqldump в PATH
 * - Наличие mysql клиента в PATH
 * - Подключение к базе данных
 * - Права доступа
 * 
 * Использование:
 *   php script_ai/check_backup_requirements.php
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "  Проверка требований для бекапирования\n";
echo "========================================\n\n";

// 1. Проверка mysqldump
echo "[1] Проверка mysqldump...\n";
$mysqldumpPath = findCommand('mysqldump');

if ($mysqldumpPath) {
    echo "    [✓] Найден: $mysqldumpPath\n";
    
    // Получаем версию
    exec("$mysqldumpPath --version", $output);
    echo "    [✓] Версия: " . trim($output[0]) . "\n\n";
} else {
    echo "    [✗] mysqldump НЕ найден в PATH\n";
    echo "    [!] Установите MySQL или добавьте путь в PATH\n";
    echo "    [!] Windows (Laragon): C:\\laragon\\bin\\mysql\\mysql-x.x.x\\bin\\\n";
    echo "    [!] Linux: /usr/bin/ или /usr/local/bin/\n\n";
}

// 2. Проверка mysql клиента
echo "[2] Проверка mysql клиента...\n";
$mysqlPath = findCommand('mysql');

if ($mysqlPath) {
    echo "    [✓] Найден: $mysqlPath\n";
    
    // Получаем версию
    exec("$mysqlPath --version", $output);
    echo "    [✓] Версия: " . trim($output[0]) . "\n\n";
} else {
    echo "    [✗] mysql клиент НЕ найден в PATH\n";
    echo "    [!] Установите MySQL для восстановления бекапов\n\n";
}

// 3. Получаем параметры подключения
echo "[3] Параметры подключения к БД:\n";
$host = Config::get('database.connections.mysql.host');
$port = Config::get('database.connections.mysql.port') ?? 3306;
$database = Config::get('database.connections.mysql.database');
$username = Config::get('database.connections.mysql.username');
$password = Config::get('database.connections.mysql.password');

echo "    Хост: $host:$port\n";
echo "    База: $database\n";
echo "    Пользователь: $username\n";
echo "    Пароль: " . (empty($password) ? '[НЕ УСТАНОВЛЕН]' : '[установлен]') . "\n\n";

// 4. Проверка подключения к БД
echo "[4] Проверка подключения к БД...\n";
try {
    $connection = DB::connection()->getPDO();
    
    if ($connection) {
        echo "    [✓] Подключение успешно!\n\n";
        
        // 5. Получаем информацию о БД
        echo "[5] Информация о базе данных:\n";
        
        // Размер БД
        try {
            $size = DB::select("SELECT 
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb 
                FROM information_schema.tables 
                WHERE table_schema = ?", [$database]);
            
            if (!empty($size) && $size[0]->size_mb) {
                echo "    Размер БД: " . $size[0]->size_mb . " MB\n";
            }
        } catch (\Exception $e) {
            echo "    [!] Не удалось получить размер БД\n";
        }
        
        // Количество таблиц
        try {
            $tables = DB::select("SELECT COUNT(*) as count FROM information_schema.tables 
                                 WHERE table_schema = ? AND table_type = 'BASE TABLE'", [$database]);
            
            if (!empty($tables)) {
                echo "    Таблиц: " . $tables[0]->count . "\n";
            }
        } catch (\Exception $e) {
            echo "    [!] Не удалось получить количество таблиц\n";
        }
        
        // Версия MySQL
        try {
            $version = DB::selectOne("SELECT version() as version");
            echo "    MySQL версия: " . $version->version . "\n";
        } catch (\Exception $e) {
            // Ignore
        }
        
        echo "\n";
        
    }
} catch (\Exception $e) {
    echo "    [✗] Ошибка подключения!\n";
    echo "    Сообщение: " . $e->getMessage() . "\n\n";
    exit(1);
}

// 6. Проверка директории для бекапов
echo "[6] Проверка директории для бекапов:\n";
$backupDir = __DIR__ . '/backups';

if (is_dir($backupDir)) {
    echo "    [✓] Директория существует: $backupDir\n";
    
    $files = array_diff(scandir($backupDir), ['.', '..']);
    echo "    [✓] Файлов: " . count($files) . "\n";
    
    if (count($files) > 0) {
        $sizes = [];
        foreach ($files as $file) {
            $sizes[] = filesize($backupDir . '/' . $file);
        }
        
        $totalSize = array_sum($sizes);
        echo "    [✓] Общий размер: " . formatBytes($totalSize) . "\n";
    }
} else {
    echo "    [!] Директория не существует\n";
    echo "    [!] Она будет создана при первом бекапе\n";
}

// 7. Проверка свободного места
echo "\n[7] Свободное место на диске:\n";
$parentDir = dirname($backupDir);
$freeSpace = disk_free_space($parentDir);

if ($freeSpace !== false) {
    $freeFormatted = formatBytes($freeSpace);
    echo "    [✓] Свободно: $freeFormatted\n";
    
    if ($freeSpace < 100 * 1024 * 1024) { // Менее 100 MB
        echo "    [!] ПРЕДУПРЕЖДЕНИЕ: Мало свободного места!\n";
    }
} else {
    echo "    [!] Не удалось определить свободное место\n";
}

// 8. Итоговая рекомендация
echo "\n========================================\n";
echo "  Итоги проверки\n";
echo "========================================\n\n";

$readyForBackup = $mysqldumpPath && $connection;

if ($readyForBackup) {
    echo "[✓] Система готова к резервному копированию!\n\n";
    echo "Вы можете использовать:\n";
    echo "  php script_ai/backup_database.php\n";
    echo "  php script_ai/auto_backup_database.php\n";
} else {
    echo "[✗] Система НЕ полностью готова к резервному копированию\n\n";
    echo "Необходимо:\n";
    
    if (!$mysqldumpPath) {
        echo "  - Установить/настроить mysqldump\n";
    }
    
    if (!$connection) {
        echo "  - Проверить подключение к базе данных\n";
    }
}

echo "\n";

/**
 * Поиск команды в PATH
 */
function findCommand($command) {
    $isWindows = stripos(PHP_OS, 'WIN') === 0;
    
    if ($isWindows) {
        // Windows
        $output = [];
        exec("where $command 2>nul", $output);
        return !empty($output) ? $output[0] : null;
    } else {
        // Linux/Mac
        $output = [];
        exec("which $command", $output);
        return !empty($output) ? $output[0] : null;
    }
}

/**
 * Форматирует размер файла в читаемый формат
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
