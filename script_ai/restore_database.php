<?php

/**
 * Скрипт для восстановления базы данных из бекапа
 * 
 * Использование:
 *   php script_ai/restore_database.php <номер_бекапа>
 *   php script_ai/restore_database.php 1  # восстановить из первого бекапа в списке
 *   
 * Если номер не указан, покажет доступные бекапы
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

$backupDir = __DIR__ . '/backups';

// Получаем доступные бекапы
$backups = array_diff(scandir($backupDir), ['.', '..']);
rsort($backups);

// Показываем список бекапов если нет аргумента
if (empty($argv[1]) || !is_numeric($argv[1])) {
    echo "=== Доступные бекапы базы $database ===\n\n";
    
    if (empty($backups)) {
        echo "[!] Бекапов не найдено!\n";
        exit(1);
    }
    
    foreach ($backups as $idx => $backup) {
        if (is_file($backupDir . '/' . $backup)) {
            $size = formatBytes(filesize($backupDir . '/' . $backup));
            $date = filemtime($backupDir . '/' . $backup);
            echo sprintf("[%d] %s (%s, %s)\n", $idx + 1, $backup, $size, date('Y-m-d H:i:s', $date));
        }
    }
    
    echo "\nДля восстановления выполните:\n";
    echo "  php script_ai/restore_database.php <номер>\n";
    echo "\nНапример:\n";
    echo "  php script_ai/restore_database.php 1\n";
    exit(0);
}

$backupIndex = (int)$argv[1] - 1;

if (!isset($backups[$backupIndex])) {
    echo "[✗] Бекап с номером " . ($backupIndex + 1) . " не найден!\n";
    exit(1);
}

$backupFile = $backupDir . '/' . $backups[$backupIndex];

echo "[!] ВНИМАНИЕ: Вы собираетесь восстановить базу данных из бекапа!\n";
echo "[!] Текущие данные будут ПЕРЕЗАПИСАНЫ!\n\n";

echo "Бекап: " . basename($backupFile) . "\n";
echo "Размер: " . formatBytes(filesize($backupFile)) . "\n";
echo "Дата: " . date('Y-m-d H:i:s', filemtime($backupFile)) . "\n";
echo "База: $database\n\n";

echo "Для подтверждения введите 'да': ";
$handle = fopen("php://stdin", "r");
$confirm = trim(fgets($handle));
fclose($handle);

if ($confirm !== 'да' && $confirm !== 'yes') {
    echo "[✗] Восстановление отменено.\n";
    exit(0);
}

echo "\n[*] Восстанавливаю базу данных...\n";

// Формируем команду восстановления
$passwordOption = !empty($password) ? "-p" . escapeshellarg($password) : '';
$command = "mysql -h $host -P $port -u $username {$passwordOption} " . escapeshellarg($database) . " < " . escapeshellarg($backupFile);

$output = [];
$returnCode = 0;
exec($command, $output, $returnCode);

if ($returnCode === 0) {
    echo "[✓] База данных успешно восстановлена!\n";
    echo "[✓] Файл: " . basename($backupFile) . "\n";
    echo "[✓] Дата восстановления: " . date('Y-m-d H:i:s') . "\n";
} else {
    echo "[✗] Ошибка при восстановлении базы!\n";
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
