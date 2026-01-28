# Инструкции для AI агентов (Copilot)

## Обзор проекта

**Cmodul** — Laravel 12 система управления складом и конфигурациями, построенная на базе современной PHP архитектуры. Основана на **модели Product (Detail)** с двумя типами:
- **Type 1**: Конфигурации (кабинеты, комплекты) — имеют **виртуальные остатки**
- **Type 2**: Компоненты — имеют **физические остатки** в таблице `stocks`

### Основные компоненты

- **Backend**: Laravel 12 с PHP 8.2+
- **Frontend**: Tailwind CSS 4 + Vite для HMR разработки  
- **БД**: SQLite (по умолчанию) или MySQL
- **Тестирование**: PHPUnit 11 + Mockery
- **Сборка**: npm + Vite для JS/CSS; `composer` для PHP

## Архитектура: Система Складских Остатков

### Ключевая концепция: Виртуальные Остатки
**Type 1 (Конфигурация)** — это комбинация **Type 2 (компонентов)**. Виртуальный остаток конфигурации = **минимум доступных компонентов** ÷ требуемое количество.

Пример: Конфигурация "Шкаф A" требует 2x ComponentX + 3x ComponentY
- ComponentX доступно: 10 → можем собрать 5 шкафов
- ComponentY доступно: 12 → можем собрать 4 шкафа  
- **Виртуальный остаток = 4 (лимитирующий компонент: ComponentY)**

### Модели и связи
- **Detail** (таблица `products`) — унифицированная модель для всех товаров с `product_type_id` (1 или 2)
  - Type 1 ↔ Type 2: много-ко-многим через таблицу `configs` (master_id ↔ slave_id)
  - `$config->componentsInConfiguration()` — получить компоненты конфигурации
  - `$config->getVirtualStock()` — рассчитать виртуальные остатки
- **Stock** — остатки компонентов (только Type 2)
  - Поля: `quantity` (всего), `reserved` (зарезервировано)
  - `$available = quantity - reserved` (вычисляется через `getAvailableAttribute()`)
- **StockMovement** — лог всех операций со складом
  - Типы: `IN`, `OUT`, `RESERVE`, `RELEASE`, `ADJUST`
  - Поля: `reference_type`, `reference_id`, `document_number`, `balance_before/after`
- **User** — пользователи (authenticatable)

### Маршруты и контроллеры
- `GET /virtual-stock` → страница виртуальных остатков (Type 1)
- `GET /places` → список компонентов Type 2 с пагинацией
- `GET /configurations` → каталог конфигураций Type 1 по фильтрам
- `/receipts` → операции прихода товара (ReceiptController)
- `/shipments` → операции отгрузки конфигураций (ShipmentController)
- `/details` → resource CRUD для товаров
- `resource cabinets`, `resource users` → legacy маршруты

Контроллеры используют:
- **StockBalanceController** — расчёт и отображение виртуальных остатков
- **ReceiptController** — создание движений `IN`, обновление остатков в таблице `stocks`
- **ShipmentController** — создание движений `OUT`, логирование в `stock_movements`

### Представления
- **Blade шаблоны** в `resources/views/` (структура: `details/`, `receipts/`, `shipments/`, `configurations/`)
- **Основной шаблон**: `layout.blade.php`
- **CSS**: Tailwind 4 в `resources/css/` с переменными в `variables.css`

## Разработка

### Локальный запуск
```bash
# Инициализация проекта
composer run setup

# Развитие (Laravel + очередь + логи + Vite одновременно)
composer run dev
# Запускает: php artisan serve, queue:listen, pail (логи), npm run dev

# Отдельно:
php artisan serve          # Только Laravel сервер на :8000
npm run dev                # Только Vite HMR

# Перед тестами ВСЕГДА:
php artisan config:clear --ansi
php artisan test
```

### Ключевые конфигурации
- `.env` — переменные окружения (см. `.env.example`)
- `config/database.php` — подключение БД (sqlite по умолчанию)
- `phpunit.xml` — настройки тестирования (in-memory sqlite БД)
- `vite.config.js` — HMR и обработка CSS/JS

## Соглашения и паттерны

### Модели (Eloquent)
- Используется `$fillable` для массового присваивания
- `$table` явно указана при нестандартных именах таблиц (например, `Detail` использует `products`)
- Методы связей: `belongsTo()`, `hasOne()`, `belongsToMany()`, `hasMany()`
- Вычисляемые атрибуты через `$appends` и методы `get{Attribute}Attribute()` (пример: `Stock->available`)

### Контроллеры
- Наследуют `App\Http\Controllers\Controller`
- Используют `Request` для валидации: `$request->validate(['field' => 'rules'])`
- Возвращают `view()` или `redirect()` с методом `->with()` для flash данных
- **Специфика**: ReceiptController и ShipmentController работают с `StockMovement` и `Stock`

### Бизнес-логика: Расчёт Виртуальных Остатков
```php
// Получить виртуальный остаток конфигурации
$config = Detail::where('product_type_id', 1)->first();
$virtualStock = $config->getVirtualStock(); // ['quantity' => int, 'limiting_component' => Detail]

// Компоненты конфигурации с количеством (через pivot->quantity)
$components = $config->componentsInConfiguration()->get();
foreach ($components as $comp) {
    $requiredQty = $comp->pivot->quantity; // сколько нужно этого компонента
    $available = $comp->stock->available;  // сколько есть на складе
}
```

### Валидация
Правила указаны в методах контроллеров:
```php
$request->validate([
    'product_id' => 'required|exists:products,id',
    'quantity' => 'required|integer|min:1',
    'document_number' => 'unique:stock_movements,document_number'
]);
```

### Представления
- Используют Blade синтаксис `{{ $variable }}` для экранирования
- Условия: `@if`, `@foreach`, `@forelse`
- Flash сообщения: `{{ session('success') }}` или `session('error')`
- Стилизация только Tailwind классами (нет inline styles)

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
- Tailwind CSS: 4.0 (классическая архитектура с PostCSS)

### Тестирование
- **PHPUnit**: ^11.5.3
- **Mockery**: ^1.6 (для мокирования)
- **Faker**: ^1.23 (для генерации тестовых данных)

## Типичные задачи

### Добавить новый контроллер для работы со складом
```bash
php artisan make:controller NewStockController

# Добавить маршруты в routes/web.php:
Route::get('/new-stock', [NewStockController::class, 'index']);
Route::post('/new-stock', [NewStockController::class, 'store']);
```

### Работать с конфигурациями и компонентами
```php
// Type 1: Конфигурация
$config = Detail::where('product_type_id', 1)->first();

// Получить все компоненты конфигурации с требуемым количеством
$components = $config->componentsInConfiguration()->with('stock')->get();
foreach ($components as $comp) {
    $qty = $comp->pivot->quantity;        // требуемое количество
    $available = $comp->stock->available; // доступно = quantity - reserved
}

// Рассчитать виртуальный остаток (сколько конфигураций можно собрать)
$virtual = $config->getVirtualStock();
echo "Можно собрать: " . $virtual['quantity'];
echo "Лимитирующий: " . $virtual['limiting_component']->name;
```

### Создать операцию прихода товара (Receipt)
```php
// ReceiptController создаёт StockMovement типа 'IN' и обновляет Stock
// Примерный алгоритм:
$stock = Stock::where('product_id', $productId)->first();
$stock->quantity += $quantity;
$stock->save();

StockMovement::create([
    'product_id' => $productId,
    'movement_type' => 'IN',
    'quantity' => $quantity,
    'document_number' => 'РКО-001',
    'balance_before' => $oldQty,
    'balance_after' => $stock->quantity
]);
```

### Запустить тесты
```bash
# Очистить кеш конфигурации перед тестами (ОБЯЗАТЕЛЬНО!)
php artisan config:clear --ansi

# Запустить все тесты
php artisan test

# Запустить конкретный тест
php artisan test tests/Feature/DetailTest.php

# Запустить тест с выводом отладки
php artisan test --debug
```

### Создать Blade шаблон со списком товаров
```blade
@forelse($items as $item)
    <tr>
        <td>{{ $item->name }}</td>
        <td>{{ $item->productType->name }}</td>
        @if($item->product_type_id == 2)
            <td>{{ $item->stock->available ?? 0 }}</td>
        @else
            <td>{{ $item->getVirtualStock()['quantity'] ?? 0 }}</td>
        @endif
    </tr>
@empty
    <tr><td colspan="3">Товары не найдены</td></tr>
@endforelse
```

## Типичные ошибки

1. **Забыли очистить кеш перед тестами** → `php artisan config:clear --ansi`
2. **Вызвали `getVirtualStock()` на Type 2 компоненте** → вернёт null, проверяйте `product_type_id == 1`
3. **Не указали `with('stock')` для Type 2 деталей** → n+1 запросы при доступе к остаткам
4. **Создали скрипты вне `script_ai/`** → нарушение жесткого правила размещения
5. **Не добавили маршрут в `routes/web.php`** → контроллер не будет доступен
6. **Используете `reserved` без логики обновления** → остатки не синхронизируются

## Дополнительные ресурсы

- [Laravel 12 документация](https://laravel.com/docs/12.x)
- [Blade синтаксис](https://laravel.com/docs/12.x/blade)
- [Eloquent ORM](https://laravel.com/docs/12.x/eloquent)
- Локальная документация: `script_ai/STOCK_STRUCTURE_DESIGN.md`

## Структура файлов (ключевые пути)

```
app/
  Http/Controllers/
    DetailController.php         # CRUD для товаров (Detail)
    StockBalanceController.php   # Расчёт и отображение виртуальных остатков
    ReceiptController.php        # Операции прихода (создание StockMovement IN)
    ShipmentController.php       # Операции отгрузки (создание StockMovement OUT)
    ConfigController.php         # Управление конфигурациями (Type 1)
    CabinetController.php        # Legacy контроллер
    UserController.php           # Управление пользователями
  Models/
    Detail.php                   # Основная модель (products таблица, Type 1 и 2)
    Stock.php                    # Остатки компонентов (только Type 2)
    StockMovement.php            # Логирование операций со складом
    ProductType.php              # Справочник типов (1=Config, 2=Component)
    Category.php, Source.php     # Справочники
    PlaceStock.php, PlaceStockMovement.php, PlaceStockSnapshot.php  # Legacy модели

resources/
  views/
    details/                     # Шаблоны CRUD для товаров
    receipts/                    # Шаблоны приходов товара
    shipments/                   # Шаблоны отгрузок конфигураций
    configurations/              # Шаблоны управления конфигурациями
    layout.blade.php             # Основной шаблон
  css/
    app.css                      # Главный CSS с Tailwind
    variables.css                # Переменные для глобальных цветов/размеров

routes/web.php                   # Все веб-маршруты

config/
  database.php                   # Конфигурация подключений БД
  app.php                        # Основной конфиг

database/
  migrations/
    *create_stocks_table.php     # Таблица остатков
    *create_stock_movements_table.php  # Таблица логирования операций
  seeders/                       # Заполнение данных БД
  factories/                     # Фабрики для тестов (UserFactory)

script_ai/                       # 📍 ИСКЛЮЧИТЕЛЬНО для AI скриптов и дебаги
  check_virtual_stock_data.php   # Проверка расчёта виртуальных остатков
  database_structure.php         # Инспекция структуры БД
  STOCK_STRUCTURE_DESIGN.md      # Документация системы остатков

tests/
  Feature/                       # Функциональные тесты
  Unit/                          # Модульные тесты
```
