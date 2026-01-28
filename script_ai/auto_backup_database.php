<?php

/**
 * Скрипт для автоматического бекапа базы данных
 * 
 * Хранит заданное количество последних бекапов
 * Удаляет старые бекапы автоматически
 * 
 * Использование:
 *   php script_ai/auto_backup_database.php [<max_backups>]
 *   php script_ai/auto_backup_database.php 10  # хранить последние 10 бекапов
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Config;

// Максимальное количество хранимых бекапов (по умолчанию 7)
$maxBackups = isset($argv[1]) ? (int)$argv[1] : 7;

$host = Config::get('database.connections.mysql.host');
$port = Config::get('database.connections.mysql.port') ?? 3306;
$database = Config::get('database.connections.mysql.database');
$username = Config::get('database.connections.mysql.username');
$password = Config::get('database.connections.mysql.password');

$backupDir = __DIR__ . '/backups';

// Создаем директорию для бекапов если её нет
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

// Формируем имя файла с датой и временем
$timestamp = date('Y-m-d_H-i-s');
$backupFile = $backupDir . '/' . $database . '_' . $timestamp . '.sql';

echo "[*] Начинаю автоматический бекап базы: $database\n";
echo "[*] Хост: $host:$port\n";
echo "[*] Максимум бекапов: $maxBackups\n\n";

// Формируем команду mysqldump
$passwordOption = !empty($password) ? "-p" . escapeshellarg($password) : '';
$command = "mysqldump -h $host -P $port -u $username {$passwordOption} " . escapeshellarg($database) . " > " . escapeshellarg($backupFile);

$output = [];
$returnCode = 0;
exec($command, $output, $returnCode);

if ($returnCode === 0 && file_exists($backupFile)) {
    $fileSize = filesize($backupFile);
    $fileSizeFormatted = formatBytes($fileSize);
    
    echo "[✓] Бекап успешно создан!\n";
    echo "[✓] Файл: " . basename($backupFile) . "\n";
    echo "[✓] Размер: $fileSizeFormatted\n\n";
    
    // Удаляем старые бекапы если превышен лимит
    $backups = array_diff(scandir($backupDir), ['.', '..']);
    usort($backups, function($a, $b) use ($backupDir) {
        $aFile = $backupDir . '/' . $a;
        $bFile = $backupDir . '/' . $b;
        return filemtime($bFile) - filemtime($aFile); // Сортируем по времени убыванию
    });
    
    if (count($backups) > $maxBackups) {
        echo "[*] Удаляю старые бекапы (превышен лимит $maxBackups):\n";
        
        $toDelete = array_slice($backups, $maxBackups);
        foreach ($toDelete as $oldBackup) {
            $oldFile = $backupDir . '/' . $oldBackup;
            if (is_file($oldFile)) {
                $oldSize = formatBytes(filesize($oldFile));
                unlink($oldFile);
                echo "  [✓] Удален: $oldBackup ($oldSize)\n";
            }
        }
    }
    
    // Показываем оставшиеся бекапы
    echo "\n[*] Актуальные бекапы:\n";
    $remainingBackups = array_diff(scandir($backupDir), ['.', '..']);
    usort($remainingBackups, function($a, $b) use ($backupDir) {
        $aFile = $backupDir . '/' . $a;
        $bFile = $backupDir . '/' . $b;
        return filemtime($bFile) - filemtime($aFile);
    });
    
    foreach ($remainingBackups as $idx => $backup) {
        if (is_file($backupDir . '/' . $backup)) {
            $size = formatBytes(filesize($backupDir . '/' . $backup));
            $date = filemtime($backupDir . '/' . $backup);
            $marker = ($backup === basename($backupFile)) ? ' [НОВЫЙ]' : '';
            echo sprintf("  [%d] %s (%s, %s)%s\n", $idx + 1, $backup, $size, date('Y-m-d H:i:s', $date), $marker);
        }
    }
    
} else {
    echo "[✗] Ошибка при создании бекапа!\n";
    echo "[✗] Код ошибки: $returnCode\n";
    if (!empty($output)) {
        echo "[✗] Вывод: " . implode("\n", $output) . "\n";
    }
    exit(1);
}

/**
 * Форматирует размер файла в читаемый формат
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
