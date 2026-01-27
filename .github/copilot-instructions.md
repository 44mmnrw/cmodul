# Инструкции для AI агентов (Copilot)

## Обзор проекта

**Cmodul** — Laravel 12 приложение для управления кабинетами и местами размещения. Основана на современной PHP архитектуре с Eloquent ORM, Blade шаблонизатором и Vite для сборки фронтенда.

### Основные компоненты

- **Backend**: Laravel 12 с PHP 8.2+
- **Frontend**: Tailwind CSS 4 + Vite для HMR разработки
- **БД**: SQLite (по умолчанию) или MySQL
- **Тестирование**: PHPUnit 11 + Mockery
- **Сборка**: npm + Vite для JS/CSS; `composer` для PHP

## Архитектура

### Модели и связи
- **Cabinet** — кабинеты с уникальным `cabinet_id`
- **Place** — места с `place_id`
- **User** — пользователи (authenticatable)
- **Связь**: Cabinet ↔ Place через pivot таблицу `cabinet_place` (many-to-many)

Пример использования:
```php
$cabinet = Cabinet::find(1);
$places = $cabinet->places; // загрузит все места кабинета
```

### Маршруты и контроллеры
- `GET /` → главная страница
- `GET /places` → список мест (из `Place::all()`)
- `resource cabinets` → CRUD для CabinetController
- `resource users` → CRUD для UserController

Контроллеры находятся в `app/Http/Controllers/` и используют:
- Laravel validation для валидации входных данных
- Route model binding (автоматическое разрешение моделей)
- Redirect с flash сообщениями (`->with('success', '...')`)

### Представления
- **Blade шаблоны** в `resources/views/`
- **Структура**: `layout.blade.php` основной шаблон
- **CSS**: Tailwind 4 в `resources/css/` с переменными в `variables.css`
- **JS**: минимум JS, основной код в `resources/js/app.js`

## Разработка

### Локальный запуск
```bash
# Инициализация проекта
composer run setup

# Развитие (все сервисы одновременно)
composer run dev

# Только сервер
php artisan serve

# Только вроде вотчер
npm run dev

# Очистка кеша конфигурации перед тестами
php artisan config:clear --ansi

# Запуск тестов
php artisan test
```

### Ключевые конфигурации
- `.env` — переменные окружения (см. `.env.example`)
- `config/database.php` — подключение БД (sqlite по умолчанию)
- `phpunit.xml` — настройки тестирования (in-memory sqlite БД для тестов)
- `vite.config.js` — HMR и обработка CSS/JS

## Соглашения и паттерны

### Модели (Eloquent)
- Используется `$fillable` для массового присваивания
- `$table` явно указана при нестандартных именах таблиц
- Методы связей возвращают `belongsToMany`, `hasMany` и т.д.

### Контроллеры
- Наследуют `App\Http\Controllers\Controller`
- Используют `Request` для валидации: `$request->validate(['field' => 'rules'])`
- Возвращают `view()` или `redirect()` с методом `->with()` для flash данных

### Валидация
Правила указаны в методах контроллеров (examples):
```php
$request->validate([
    'cabinet_id' => 'required|unique:cabinets,cabinet_id',
    'name' => 'required|string',
]);
```

### Представления
- Используют Blade синтаксис `{{ $variable }}` для экранирования
- Условия: `@if`, `@foreach`, `@forelse`
- Flash сообщения: `{{ session('success') }}`

## ⚠️ ЖЕСТКОЕ ПРАВИЛО: Размещение скриптов и тестов

**ВСЕ сервисные скрипты, тесты, проверки, дебаги создаются ТОЛЬКО в папке:**
```
C:\laragon\www\Cmodul\script_ai\
```

Это включает:
- Временные скрипты для анализа данных
- Утилиты для миграции или синхронизации
- Дебаг скрипты для проверки логики
- Специальные тесты или проверки
- Любые вспомогательные PHP/JS файлы

**Исключение**: Официальные модульные тесты в `tests/` (PHPUnit) могут изменяться согласно `phpunit.xml`.

## Зависимости и версии

### Критические версии
- PHP: ^8.2
- Laravel: ^12.0
- Node: поддерживает современные npm пакеты
- Tailwind CSS: 4.0 (использует `@tailwindcss/vite`)

### Тестирование
- **PHPUnit**: ^11.5.3
- **Mockery**: ^1.6 (для мокирования)
- **Faker**: ^1.23 (для генерации тестовых данных)

## Типичные задачи

### Добавить новую модель с контроллером
```bash
php artisan make:model Cabinet -c
# Обновить миграцию в database/migrations/
# Добавить маршруты в routes/web.php
# Создать Blade views в resources/views/{model}/
```

### Работать со связями
```php
// Загрузить связанные данные
$cabinets = Cabinet::with('places')->get();

// Добавить запись в pivot таблицу
$cabinet->places()->attach($placeId);

// Удалить из связи
$cabinet->places()->detach($placeId);
```

### Стилизовать компоненты
- Используются Tailwind классы (`class="flex justify-center"`)
- Переменные в `resources/css/variables.css` для глобальных цветов/размеров
- Импорты CSS: `@import` в `resources/css/app.css`

### Запустить тесты
```bash
# Очистить кеш конфигурации перед тестами
php artisan config:clear --ansi

# Запустить все тесты
php artisan test

# Запустить конкретный тест
php artisan test tests/Feature/ExampleTest.php
```

## Структура файлов (ключевые пути)

```
app/
  Http/Controllers/     # Обработчики маршрутов (Cabinet, Place, User)
  Models/               # Eloquent модели (Cabinet, Place, User)
  Providers/            # Service providers (AppServiceProvider)

resources/
  views/                # Blade шаблоны
    cabinets/           # Представления для кабинетов
    places/             # Представления для мест
    users/              # Представления для пользователей
    layout.blade.php    # Основной шаблон
  css/                  # Tailwind CSS (app.css, variables.css)
  js/                   # JavaScript (app.js, bootstrap.js)

routes/web.php          # Определение всех веб-маршрутов

config/
  database.php          # Конфигурация подключений БД
  app.php               # Основной конфиг приложения

database/
  migrations/           # Миграции БД
  seeders/              # Заполнение данных БД
  factories/            # Фабрики для тестов

script_ai/              # 📍 ИСКЛЮЧИТЕЛЬНО для AI скриптов и дебаги
tests/                  # PHPUnit тесты (Feature, Unit)
```

## Частые ошибки

1. **Забыли очистить кеш перед тестами** → `php artisan config:clear --ansi`
2. **Используете `$table` свойство с неправильным названием** → проверьте имя таблицы в миграции
3. **Забыли добавить маршрут в `routes/web.php`** → контроллер не будет доступен
4. **Не указали `$fillable` в модели** → массовое присваивание будет проигнорировано
5. **Создали скрипты вне `script_ai/`** → нарушение жесткого правила размещения

## Дополнительные ресурсы

- [Laravel документация](https://laravel.com/docs)
- [Tailwind CSS](https://tailwindcss.com)
- [Blade синтаксис](https://laravel.com/docs/blade)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
