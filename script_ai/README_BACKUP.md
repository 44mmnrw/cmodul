# Скрипты резервного копирования базы данных

## 📋 Описание

Набор скриптов для управления резервными копиями MySQL базы данных проекта Cmodul.

## 🛠️ Доступные скрипты

### 1. `backup_database.php` — Создание бекапа

Создает полный SQL дамп текущей базы данных с меткой времени.

**Использование:**
```bash
php script_ai/backup_database.php
```

**Что происходит:**
- Создает директорию `script_ai/backups/` если её нет
- Создает SQL файл с именем: `<database_name>_YYYY-MM-DD_HH-MM-SS.sql`
- Показывает список всех доступных бекапов
- Выводит размер и дату каждого бекапа

**Пример вывода:**
```
[✓] Бекап успешно создан!
[✓] Файл: cmodul_2026-01-28_14-30-45.sql
[✓] Размер: 2.45 MB
[✓] Строк в файле: 12450

[*] Доступные бекапы:
  [1] cmodul_2026-01-28_14-30-45.sql (2.45 MB, 2026-01-28 14:30:45)
  [2] cmodul_2026-01-28_10-15-30.sql (2.40 MB, 2026-01-28 10:15:30)
```

---

### 2. `restore_database.php` — Восстановление из бекапа

Восстанавливает базу данных из выбранного бекапа.

**Использование:**
```bash
# Показать доступные бекапы
php script_ai/restore_database.php

# Восстановить из конкретного бекапа
php script_ai/restore_database.php 1
```

**Что происходит:**
- Показывает список всех доступных бекапов с номерами
- Требует подтверждение перед восстановлением (введите `да` или `yes`)
- Восстанавливает базу из выбранного SQL файла
- Показывает статус операции

**⚠️ ВАЖНО:** 
- Текущие данные в базе будут **ПЕРЕЗАПИСАНЫ**!
- Требуется явное подтверждение введением слова `да` или `yes`

**Пример использования:**
```bash
$ php script_ai/restore_database.php
=== Доступные бекапы базы cmodul ===

[1] cmodul_2026-01-28_14-30-45.sql (2.45 MB, 2026-01-28 14:30:45)
[2] cmodul_2026-01-28_10-15-30.sql (2.40 MB, 2026-01-28 10:15:30)

Для восстановления выполните:
  php script_ai/restore_database.php <номер>

$ php script_ai/restore_database.php 1
[!] ВНИМАНИЕ: Вы собираетесь восстановить базу данных из бекапа!
[!] Текущие данные будут ПЕРЕЗАПИСАНЫ!

Бекап: cmodul_2026-01-28_14-30-45.sql
Размер: 2.45 MB
Дата: 2026-01-28 14:30:45
База: cmodul

Для подтверждения введите 'да': да

[*] Восстанавливаю базу данных...
[✓] База данных успешно восстановлена!
```

---

### 3. `auto_backup_database.php` — Автоматический бекап с ротацией

Создает бекап и автоматически удаляет старые копии (ротация).

**Использование:**
```bash
# Бекап с хранением последних 7 копий (по умолчанию)
php script_ai/auto_backup_database.php

# Бекап с хранением последних 10 копий
php script_ai/auto_backup_database.php 10
```

**Что происходит:**
- Создает новый бекап
- Если превышен лимит, удаляет самые старые бекапы
- Показывает актуальный список оставшихся бекапов
- Отмечает новый бекап в списке `[НОВЫЙ]`

**Пример вывода:**
```
[*] Начинаю автоматический бекап базы: cmodul
[*] Хост: localhost:3306
[*] Максимум бекапов: 7

[✓] Бекап успешно создан!
[✓] Файл: cmodul_2026-01-28_14-35-12.sql
[✓] Размер: 2.45 MB

[*] Актуальные бекапы:
  [1] cmodul_2026-01-28_14-35-12.sql (2.45 MB, 2026-01-28 14:35:12) [НОВЫЙ]
  [2] cmodul_2026-01-28_14-30-45.sql (2.45 MB, 2026-01-28 14:30:45)
  [3] cmodul_2026-01-28_10-15-30.sql (2.40 MB, 2026-01-28 10:15:30)
```

---

## 🔧 Настройка периодического бекапа

### Вариант 1: Windows Task Scheduler

1. Откройте **Task Scheduler** (Планировщик задач)
2. Создайте новую задачу:
   - Имя: `Cmodul Database Backup`
   - Триггер: Ежедневно в 2:00 AM
   - Действие: Запустить программу
     - Программа: `C:\laragon\bin\php\php-8.2.x\php.exe`
     - Аргументы: `C:\laragon\www\Cmodul\script_ai\auto_backup_database.php 7`

### Вариант 2: Cron (Linux/Mac)

Добавьте в crontab:
```bash
# Ежедневно в 2:00 AM
0 2 * * * cd /path/to/cmodul && php script_ai/auto_backup_database.php 7

# Каждые 6 часов
0 */6 * * * cd /path/to/cmodul && php script_ai/auto_backup_database.php 10
```

### Вариант 3: Laravel Scheduler

Добавьте в `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    // Ежедневный бекап в 2:00 AM, хранение 7 последних копий
    $schedule->exec('php script_ai/auto_backup_database.php 7')
        ->dailyAt('02:00')
        ->appendOutputTo(storage_path('logs/backup.log'));
}
```

Затем запустите Laravel Scheduler:
```bash
php artisan schedule:run
```

---

## 📂 Структура файлов бекапов

Все бекапы сохраняются в `script_ai/backups/`:
```
script_ai/
├── backups/
│   ├── cmodul_2026-01-28_14-30-45.sql   (2.45 MB)
│   ├── cmodul_2026-01-28_10-15-30.sql   (2.40 MB)
│   ├── cmodul_2026-01-27_22-00-15.sql   (2.38 MB)
│   └── ...
├── backup_database.php
├── restore_database.php
├── auto_backup_database.php
└── README_BACKUP.md
```

---

## ⚙️ Требования

- **MySQL**: Установлена на системе в PATH
- **PHP**: 7.4+ (для выполнения скриптов)
- **Laravel**: Проект должен быть инициализирован (`.env` файл настроен)

Проверьте наличие `mysqldump` и `mysql`:
```bash
# Windows
where mysqldump
where mysql

# Linux/Mac
which mysqldump
which mysql
```

---

## 🚨 Восстановление в критической ситуации

Если скрипты PHP не работают, можно восстановить базу напрямую через командную строку:

```bash
# Windows
mysql -h localhost -u root -p cmodul < script_ai/backups/cmodul_2026-01-28_14-30-45.sql

# Linux/Mac
mysql -h localhost -u root -p cmodul < script_ai/backups/cmodul_2026-01-28_14-30-45.sql
```

Замените:
- `localhost` — адрес хоста БД
- `root` — имя пользователя
- `cmodul` — имя базы данных
- Путь к файлу бекапа

---

## 📝 Логирование

Для логирования результатов бекапов в файл:

```bash
# Добавить в конец файла логов
php script_ai/backup_database.php >> storage/logs/backup.log 2>&1

# Или с временной меткой
php script_ai/backup_database.php >> storage/logs/backup.log 2>&1 &
```

---

## 🔐 Безопасность

1. **Пароль БД в .env**: Хранится в защищенном файле `.env` (в `.gitignore`)
2. **SQL файлы**: Содержат полные данные БД, храните в защищенном месте
3. **Удаление старых копий**: Автоматическая ротация предотвращает переполнение диска
4. **Резервное копирование бекапов**: 
   - Сохраняйте копии на облачное хранилище (Google Drive, OneDrive, AWS S3)
   - Используйте синхронизацию `script_ai/backups/` в облако

---

## 🐛 Решение проблем

### Проблема: `mysqldump: command not found`

**Решение:** MySQL не в PATH. Укажите полный путь:

```bash
# Windows (Laragon)
"C:\laragon\bin\mysql\mysql-8.0.x\bin\mysqldump.exe" -h localhost -u root ...

# Linux
/usr/bin/mysqldump -h localhost -u root ...
```

### Проблема: `Access denied for user 'root'@'localhost'`

**Решение:** Проверьте пароль в `.env` файле:

```env
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=cmodul
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

### Проблема: Бекап слишком большой

**Решение:** Используйте сжатие:

```bash
mysqldump ... | gzip > backup.sql.gz
```

---

## 📞 Поддержка

При возникновении ошибок проверьте:
1. Доступность MySQL (`mysql -V`)
2. Подключение к БД (`php artisan tinker` → `DB::connection()->getPDO()`)
3. Права на запись в `script_ai/backups/`
4. Свободное место на диске

