<?php

/**
 * Скрипт для снятия бекапа базы данных
 * 
 * Использование:
 *   php script_ai/backup_database.php
 *   
 * Создает SQL дамп в папке script_ai/backups/ с указанием даты и времени
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

// Определяем параметры подключения к БД
$host = Config::get('database.connections.mysql.host');
$port = Config::get('database.connections.mysql.port') ?? 3306;
$database = Config::get('database.connections.mysql.database');
$username = Config::get('database.connections.mysql.username');
$password = Config::get('database.connections.mysql.password');

// Создаем директорию для бекапов если её нет
$backupDir = __DIR__ . '/backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
    echo "[✓] Создана директория для бекапов: $backupDir\n";
}

// Формируем имя файла с датой и временем
$timestamp = date('Y-m-d_H-i-s');
$backupFile = $backupDir . '/' . $database . '_' . $timestamp . '.sql';

// Формируем команду mysqldump
// Если пароль содержит специальные символы, нужно заключить его в кавычки
$passwordOption = !empty($password) ? "-p" . escapeshellarg($password) : '';
$command = "mysqldump -h $host -P $port -u $username {$passwordOption} " . escapeshellarg($database) . " > " . escapeshellarg($backupFile);

echo "[*] Начинаю резервное копирование базы: $database\n";
echo "[*] Хост: $host:$port\n";
echo "[*] Пользователь: $username\n";
echo "[*] Файл: $backupFile\n";
echo "[*] Дата: $timestamp\n\n";

// Выполняем команду
$output = [];
$returnCode = 0;
exec($command, $output, $returnCode);

if ($returnCode === 0 && file_exists($backupFile)) {
    $fileSize = filesize($backupFile);
    $fileSizeFormatted = formatBytes($fileSize);
    
    echo "[✓] Бекап успешно создан!\n";
    echo "[✓] Файл: " . basename($backupFile) . "\n";
    echo "[✓] Размер: $fileSizeFormatted\n";
    
    // Выводим информацию о содержимом бекапа
    $lineCount = count(file($backupFile));
    echo "[✓] Строк в файле: $lineCount\n";
    
    // Показываем список всех бекапов
    echo "\n[*] Доступные бекапы:\n";
    $backups = array_diff(scandir($backupDir), ['.', '..']);
    rsort($backups); // Сортируем по убыванию (новые первыми)
    
    foreach ($backups as $idx => $backup) {
        if (is_file($backupDir . '/' . $backup)) {
            $size = formatBytes(filesize($backupDir . '/' . $backup));
            $date = filemtime($backupDir . '/' . $backup);
            echo sprintf("  [%d] %s (%s, %s)\n", $idx + 1, $backup, $size, date('Y-m-d H:i:s', $date));
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
