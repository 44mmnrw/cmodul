# 📊 СИСТЕМА УПРАВЛЕНИЯ ОСТАТКАМИ (Stock Management)

## Иерархия продуктов

```
Type 1 (Конфигурации)      ← ВИРТУАЛЬНЫЕ ОСТАТКИ
  ↓ (через configs table)
Type 2 (Комплектующие)     ← РЕАЛЬНЫЕ ОСТАТКИ
  ↓ (через configs table)
Type 3 (Детали/Материалы)  ← РАСЧЁТНЫЕ ПОТРЕБНОСТИ
```

---

## 1. Что нужно решить?

**Задача:** Отслеживать остатки трёх типов продуктов с разными стратегиями хранения:

- **Type 2 (Комплектующие)**: Хранить РЕАЛЬНЫЕ остатки на физическом складе
- **Type 1 (Конфигурации)**: Рассчитывать ВИРТУАЛЬНЫЕ остатки на основе Type 2
- **Type 3 (Детали)**: Рассчитывать ПОТРЕБНОСТИ на основе Type 1 и Type 2

**Как связаны типы?** Через существующую таблицу `configs`:
```
Шкаф (Type 1)      → configs → Дверь (Type 2)     → configs → Стекло (Type 3)
Шкаф (Type 1)      → configs → Панель (Type 2)    → configs → Сталь (Type 3)
```

---

## 2. Структура таблиц

### Таблица `stocks` — НОВАЯ (реальные остатки Type 2)

```sql
CREATE TABLE stocks (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    product_id BIGINT UNSIGNED NOT NULL UNIQUE,
    
    quantity INT DEFAULT 0,                    -- всего на складе
    reserved INT DEFAULT 0,                    -- зарезервировано
    available INT GENERATED ALWAYS AS (quantity - reserved) STORED,
    
    min_quantity INT DEFAULT 0,                -- минимум для алерта
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product (product_id),
    UNIQUE KEY uk_product (product_id)
);
```

**Назначение:** Хранит РЕАЛЬНЫЕ остатки **только для Type 2** (комплектующих).

**Пример:**
```
| id | product_id | quantity | reserved | available |
|----|------------|----------|----------|-----------|
| 1  | 50 (Дверь) | 40       | 10       | 30        |
| 2  | 51 (Панель)| 30       | 5        | 25        |
| 3  | 52 (Основ.)| 60       | 0        | 60        |
```

---

### Таблица `stock_movements` — НОВАЯ (история операций)

```sql
CREATE TABLE stock_movements (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    product_id BIGINT UNSIGNED NOT NULL,
    
    movement_type ENUM('IN', 'OUT', 'RESERVE', 'RELEASE', 'ADJUST'),
    quantity INT NOT NULL,
    
    reference_type VARCHAR(50),       -- 'Configuration', 'Order', 'Manual'
    reference_id BIGINT UNSIGNED,
    reason TEXT,
    
    balance_before INT,
    balance_after INT,
    
    created_at TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product (product_id),
    INDEX idx_created (created_at),
    INDEX idx_reference (reference_type, reference_id),
    COMPOSITE INDEX idx_product_created (product_id, created_at)
);
```

**Назначение:** Логирует все операции для аудита и восстановления истории.

**Типы движений:**
- `IN` — приход товара на склад
- `OUT` — расход (сборка, продажа, отправка)
- `RESERVE` — зарезервировано для заказа
- `RELEASE` — отмена резервирования
- `ADJUST` — корректировка (брак, потеря, переучёт)

**Пример:**
```
| id | product_id | movement_type | quantity | reference_type | reason        | balance_before | balance_after |
|----|------------|---------------|----------|----------------|---------------|----------------|---------------|
| 1  | 50         | IN            | 50       | Manual         | Поступление   | 0              | 50            |
| 2  | 50         | RESERVE       | 10       | Configuration  | Заказ #5      | 50             | 50            |
| 3  | 50         | OUT           | 10       | Configuration  | Сборка #5     | 50             | 40            |
```

---

### Таблица `configs` — СУЩЕСТВУЮЩАЯ (структура продуктов)

```sql
CREATE TABLE configs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    master_id BIGINT UNSIGNED NOT NULL,      -- родитель (Type 1 или 2)
    slave_id BIGINT UNSIGNED NOT NULL,       -- компонент (Type 2 или 3)
    quantity INT DEFAULT 1,                  -- сколько slave в master
    
    FOREIGN KEY (master_id) REFERENCES products(id),
    FOREIGN KEY (slave_id) REFERENCES products(id),
    INDEX idx_master (master_id),
    INDEX idx_slave (slave_id)
);
```

**Использование:** Одна таблица для обеих иерархических связей:
- `Type 1 → Type 2`: конфигурация состоит из компонентов
- `Type 2 → Type 3`: компонент состоит из деталей

**Пример:**
```
| master_id | slave_id | quantity |
|-----------|----------|----------|
| 1         | 50       | 1        |  Шкаф → Дверь
| 1         | 51       | 2        |  Шкаф → Панель
| 1         | 52       | 1        |  Шкаф → Основание
| 50        | 100      | 1        |  Дверь → Стекло
| 51        | 102      | 5        |  Панель → Сталь
| 52        | 102      | 10       |  Основание → Сталь
```

---

## 3. Методы в модели Detail

### Получить компоненты Type 2 для Type 1

```php
class Detail extends Model {
    protected $table = 'products';
    
    /**
     * Получить компоненты (Type 2) этой конфигурации
     * Используется для Type 1 продуктов
     */
    public function components() {
        return $this->belongsToMany(
            Detail::class,
            'configs',
            'master_id',
            'slave_id'
        )->withPivot('quantity')
         ->whereHas('productType', function($q) {
             $q->where('id', 2);  // только Type 2
         });
    }
    
    /**
     * Получить детали (Type 3) этого компонента
     * Используется для Type 2 продуктов
     */
    public function details() {
        return $this->belongsToMany(
            Detail::class,
            'configs',
            'master_id',
            'slave_id'
        )->withPivot('quantity')
         ->whereHas('productType', function($q) {
             $q->where('id', 3);  // только Type 3
         });
    }
    
    /**
     * Получить все входящие компоненты (любого типа)
     */
    public function allComponents() {
        return $this->belongsToMany(
            Detail::class,
            'configs',
            'master_id',
            'slave_id'
        )->withPivot('quantity');
    }
    
    /**
     * Отношение к остаткам (только для Type 2)
     */
    public function stock() {
        return $this->hasOne(Stock::class, 'product_id', 'id');
    }
}
```

### Использование

```php
// Получить компоненты конфигурации
$config = Detail::find(1);  // Шкаф
$components = $config->components()->get();
// SELECT * FROM products WHERE id IN (50, 51, 52) AND product_type_id = 2

// Получить детали компонента
$door = Detail::find(50);  // Дверь
$details = $door->details()->get();
// SELECT * FROM products WHERE id IN (100, ...) AND product_type_id = 3

// Получить остаток компонента
$doorStock = $door->stock;  // вернёт объект Stock с quantity, reserved, available
echo $doorStock->available;  // 30 (доступно)
```

---

## 4. Расчёт виртуальных остатков Type 1

```php
class Detail extends Model {
    /**
     * Рассчитать доступное количество конфигураций
     * Только для Type 1!
     */
    public function getAvailableQuantity() {
        if ($this->product_type_id != 1) {
            return null;
        }
        
        $components = $this->components()->with('stock')->get();
        
        $minAvailable = PHP_INT_MAX;
        
        foreach ($components as $component) {
            if (!$component->stock) {
                return 0;  // Нет остатков → нельзя собрать
            }
            
            // Сколько конфигураций можем собрать с этим компонентом?
            $canMake = floor($component->stock->available / $component->pivot->quantity);
            $minAvailable = min($minAvailable, $canMake);
        }
        
        return $minAvailable === PHP_INT_MAX ? 0 : $minAvailable;
    }
}
```

### Пример расчёта

```
Шкаф (Type 1, id=1) требует:
  - Дверь (Type 2, id=50):      1 шт, доступно 30 → можем собрать 30 шкафов
  - Панель (Type 2, id=51):     2 шт, доступно 25 → можем собрать 12 шкафов
  - Основание (Type 2, id=52):  1 шт, доступно 60 → можем собрать 60 шкафов

ВИРТУАЛЬНЫЙ ОСТАТОК = MIN(30, 12, 60) = 12 ШКАФОВ
```

**Примеры:**
```
Шкаф (Type 1, id=1) → Дверь (Type 2, id=50) : 1 шт
Шкаф (Type 1, id=1) → Панель (Type 2, id=51) : 2 шт
Дверь (Type 2, id=50) → Стекло (Type 3, id=100) : 1 шт
Панель (Type 2, id=51) → Сталь (Type 3, id=101) : 5 кг
```

**Зачем:**
- Одна таблица для обоих уровней иерархии
- Быстро найти требования любого продукта
- Легко рассчитать виртуальные остатки и потребности

---

### Таблица: `stock_movements` (История движения)

```sql
CREATE TABLE stock_movements (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    product_id BIGINT NOT NULL,
    movement_type ENUM ('IN', 'OUT', 'RESERVE', 'RELEASE', 'ADJUST'),
    quantity INT NOT NULL,
    
    reference_type VARCHAR,  -- 'Configuration', 'Order', 'Manual'
    reference_id BIGINT,
    reason VARCHAR,
    
    balance_before INT,
    balance_after INT,
    
    user_id BIGINT,
    created_at TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    
    INDEX (product_id),
    INDEX (reference_id),
    INDEX (created_at)
);
```

**Хранит:** История всех изменений Type 2 остатков

**Типы движений:**
- `IN` — приход товара
- `OUT` — расход (сборка, продажа)
- `RESERVE` — зарезервировано
- `RELEASE` — отмена резервирования
- `ADJUST` — корректировка (брак, потеря)

---

## 5. Расчёт потребностей Type 3

## 5. Расчёт потребностей Type 3

```php
class DemandCalculator {
    
    /**
     * Рассчитать потребность в деталях Type 3
     * для производства N конфигураций Type 1
     */
    public function calculateDemandForConfiguration($configId, $quantity) {
        $config = Detail::find($configId);
        $demands = [];
        
        // Получить все компоненты Type 2 конфигурации
        $components = $config->components()->get();
        
        foreach ($components as $component) {
            $componentQty = $component->pivot->quantity * $quantity;
            
            // Для каждого компонента получить его детали Type 3
            $details = $component->details()->get();
            
            foreach ($details as $detail) {
                $detailQty = $detail->pivot->quantity * $componentQty;
                
                $demands[$detail->id] = ($demands[$detail->id] ?? 0) + $detailQty;
            }
        }
        
        return $demands;
    }
}
```

### Пример расчёта

```
Нужно собрать 10 ШКАФОВ

Требуется компонентов:
  - Дверь: 1 × 10 = 10 шт
  - Панель: 2 × 10 = 20 шт
  - Основание: 1 × 10 = 10 шт

Потребность в деталях:
  - Стекло: 1 (на дверь) × 10 = 10 шт
  - Сталь: (5 на панель × 20 + 10 на основание × 10) = 100 + 100 = 200 кг
  - Краска: 0.5 (на панель) × 20 = 10 л
```

---

## 6. Логика операций со склада

### Проверка доступности и резервирование

```php
class StockService {
    
    /**
     * Зарезервировать компоненты для сборки конфигурации
     */
    public function reserveForConfiguration($configId, $quantity) {
        $config = Detail::with('components.stock')->find($configId);
        
        // Проверить доступность
        if ($config->getAvailableQuantity() < $quantity) {
            throw new Exception('Недостаточно материалов');
        }
        
        // Резервировать каждый компонент
        foreach ($config->components as $component) {
            $needed = $component->pivot->quantity * $quantity;
            
            StockMovement::create([
                'product_id' => $component->id,
                'movement_type' => 'RESERVE',
                'quantity' => $needed,
                'reference_type' => 'Configuration',
                'reference_id' => $configId,
                'balance_before' => $component->stock->available,
                'balance_after' => $component->stock->available,  // не меняется, только reserved
            ]);
            
            $component->stock->increment('reserved', $needed);
        }
    }
    
    /**
     * Списать со склада при сборке
     */
    public function releaseForConfiguration($configId, $quantity) {
        $config = Detail::with('components.stock')->find($configId);
        
        foreach ($config->components as $component) {
            $needed = $component->pivot->quantity * $quantity;
            $stock = $component->stock;
            
            $before = $stock->available;
            $stock->decrement('quantity', $needed);
            $stock->decrement('reserved', $needed);
            
            StockMovement::create([
                'product_id' => $component->id,
                'movement_type' => 'OUT',
                'quantity' => $needed,
                'reference_type' => 'Configuration',
                'reference_id' => $configId,
                'balance_before' => $before,
                'balance_after' => $stock->available,
            ]);
        }
    }
}
```

---

## 7. Модели

### Stock Model

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model {
    protected $table = 'stocks';
    protected $fillable = ['product_id', 'quantity', 'reserved', 'min_quantity'];
    protected $appends = ['available'];
    
    public function product() {
        return $this->belongsTo(Detail::class, 'product_id');
    }
    
    public function movements() {
        return $this->hasMany(StockMovement::class, 'product_id', 'product_id');
    }
    
    // Автоматический расчёт доступного остатка
    public function getAvailableAttribute() {
        return $this->quantity - $this->reserved;
    }
}
```

### StockMovement Model

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model {
    protected $table = 'stock_movements';
    protected $fillable = [
        'product_id',
        'movement_type',
        'quantity',
        'reference_type',
        'reference_id',
        'reason',
        'balance_before',
        'balance_after'
    ];
    
    public function product() {
        return $this->belongsTo(Detail::class, 'product_id');
    }
}
```

---

## 8. Миграции

```bash
# Создавать в database/migrations/

php artisan make:migration create_stocks_table
php artisan make:migration create_stock_movements_table
```

---

## 9. Преимущества этого подхода

✅ **Минимум изменений БД** — используем существующую `configs` таблицу
✅ **Простота** — явные методы в модели для разных типов связей
✅ **История** — все операции логируются в `stock_movements`
✅ **Аудит** — можно восстановить состояние на любую дату
✅ **Расчёты** — виртуальные остатки и потребности вычисляются по требованию
✅ **Гибкость** — легко добавлять новые операции и типы движений

---

## 10. Что дальше?

1. ✓ Утвердить структуру
2. ⚪ Создать миграции для `stocks` и `stock_movements`
3. ⚪ Создать модели `Stock` и `StockMovement`
4. ⚪ Добавить методы в модель `Detail`
5. ⚪ Реализовать `StockService` для операций
6. ⚪ Добавить UI для управления остатками
7. ⚪ Добавить отчеты и аналитику

