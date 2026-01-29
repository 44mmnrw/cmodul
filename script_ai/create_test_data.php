<?php

require_once __DIR__ . '/../bootstrap/app.php';

use App\Models\Detail;
use App\Models\ProductType;
use App\Models\Stock;
use App\Models\Category;

try {
    // Создаём/получаем типы продуктов
    $typeConfig = ProductType::firstOrCreate(['id' => 1], ['name' => 'Конфигурация']);
    $typeComponent = ProductType::firstOrCreate(['id' => 2], ['name' => 'Компонент']);

    // Создаём категорию
    $category = Category::firstOrCreate(['name' => 'Электрооборудование']);

    echo "=== Создание тестовых компонентов (Type 2) ===\n";

    // Создаём компоненты (Type 2)
    $components = [
        ['name' => 'Корпус металлический', 'scu' => 'CORP-001'],
        ['name' => 'Дверь с замком', 'scu' => 'DOOR-001'],
        ['name' => 'Полка регулируемая', 'scu' => 'SHELF-001'],
        ['name' => 'Кабельный ввод', 'scu' => 'CABLE-001'],
        ['name' => 'Вентилятор 220В', 'scu' => 'FAN-001'],
    ];

    $componentIds = [];
    foreach ($components as $comp) {
        $detail = Detail::firstOrCreate(
            ['scu' => $comp['scu']],
            array_merge($comp, [
                'product_type_id' => 2,
                'category_id' => $category->id,
                'description' => 'Компонент для конфигураций',
                'weight' => 5.5,
                'height' => 100,
                'width' => 50,
                'depth' => 30,
            ])
        );

        // Создаём остатки
        Stock::firstOrCreate(
            ['product_id' => $detail->id],
            ['quantity' => rand(5, 50), 'reserved' => 0, 'min_quantity' => 5]
        );

        $componentIds[] = $detail->id;
        echo "✓ Компонент: {$detail->name} (ID: {$detail->id}, SCU: {$comp['scu']})\n";
    }

    echo "\n=== Создание конфигураций (Type 1) ===\n";

    // Создаём конфигурации (Type 1)
    $configs = [
        ['name' => 'Шкаф TC-100 RAL7035', 'scu' => 'TC-100-7035'],
        ['name' => 'Шкаф TC-100 RAL9003', 'scu' => 'TC-100-9003'],
        ['name' => 'Шкаф TC-200 RAL7035', 'scu' => 'TC-200-7035'],
    ];

    foreach ($configs as $idx => $cfg) {
        $detail = Detail::firstOrCreate(
            ['scu' => $cfg['scu']],
            array_merge($cfg, [
                'product_type_id' => 1,
                'category_id' => $category->id,
                'description' => 'Конфигурация шкафа',
                'weight' => 25.5,
                'height' => 2000,
                'width' => 1000,
                'depth' => 600,
            ])
        );

        // Добавляем компоненты в конфигурацию
        // Каждая конфигурация содержит 2-3 компонента в разных количествах
        $components_for_config = array_slice($componentIds, $idx, 3);
        
        foreach ($components_for_config as $comp_idx => $comp_id) {
            $qty = $comp_idx == 0 ? 2 : 1; // Первый компонент нужен в 2 штуках
            
            if (!$detail->componentsInConfiguration()->where('slave_id', $comp_id)->exists()) {
                $detail->componentsInConfiguration()->attach($comp_id, ['quantity' => $qty]);
                echo "  └─ Добавлен компонент (ID: {$comp_id}, кол-во: {$qty})\n";
            }
        }

        echo "✓ Конфигурация: {$detail->name} (ID: {$detail->id}, SCU: {$cfg['scu']})\n";
    }

    echo "\n✅ Тестовые данные успешно созданы!\n";

} catch (\Exception $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
    exit(1);
}
