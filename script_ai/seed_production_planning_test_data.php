<?php

use App\Models\Detail;
use App\Models\ProductType;
use App\Models\Stock;

// Получаем типы товаров
$typeConfig = ProductType::where('id', 1)->first() ?? ProductType::create(['id' => 1, 'name' => 'Configuration']);
$typeComponent = ProductType::where('id', 2)->first() ?? ProductType::create(['id' => 2, 'name' => 'Component']);

echo "Creating test data for Production Planning...\n";

// Создаем компоненты (Type 2)
$components = [
    ['name' => 'Модуль питания 48В', 'scu' => 'PSU-48-001', 'qty' => 20],
    ['name' => 'Контроллер управления', 'scu' => 'CTRL-001', 'qty' => 15],
    ['name' => 'Блок коммутации', 'scu' => 'SW-BLOCK-01', 'qty' => 10],
    ['name' => 'Кабель оптический LC-LC', 'scu' => 'CABLE-OPT-001', 'qty' => 50],
    ['name' => 'Разъем LC duplex', 'scu' => 'CONN-LC-DUP', 'qty' => 100],
];

$componentIds = [];
foreach ($components as $comp) {
    $detail = Detail::where('scu', $comp['scu'])->first();
    if (!$detail) {
        $detail = Detail::create([
            'name' => $comp['name'],
            'scu' => $comp['scu'],
            'product_type_id' => $typeComponent->id,
            'description' => 'Компонент для производства'
        ]);
        echo "✓ Created component: {$comp['name']}\n";
    }
    
    // Создаем или обновляем остатки
    $stock = $detail->stock() ?? Stock::where('product_id', $detail->id)->first();
    if (!$stock) {
        Stock::create([
            'product_id' => $detail->id,
            'quantity' => $comp['qty'],
            'reserved' => 0,
            'min_quantity' => 5
        ]);
        echo "  └─ Created stock: {$comp['qty']} шт\n";
    }
    
    $componentIds[] = $detail->id;
}

// Создаем конфигурации (Type 1)
$configs = [
    [
        'name' => 'Шкаф TC-100 RAL7035',
        'scu' => 'TC-100-7035',
        'components' => [
            $componentIds[0] => 2,  // 2x PSU
            $componentIds[1] => 1,  // 1x Controller
            $componentIds[2] => 1,  // 1x Switch
        ]
    ],
    [
        'name' => 'Шкаф TC-100 RAL9003',
        'scu' => 'TC-100-9003',
        'components' => [
            $componentIds[0] => 2,
            $componentIds[1] => 1,
            $componentIds[2] => 1,
        ]
    ],
    [
        'name' => 'Шкаф TC-200 RAL7035',
        'scu' => 'TC-200-7035',
        'components' => [
            $componentIds[0] => 4,
            $componentIds[1] => 2,
            $componentIds[2] => 2,
        ]
    ],
];

foreach ($configs as $config) {
    $detail = Detail::where('scu', $config['scu'])->first();
    if (!$detail) {
        $detail = Detail::create([
            'name' => $config['name'],
            'scu' => $config['scu'],
            'product_type_id' => $typeConfig->id,
            'description' => 'Конфигурация для заказа'
        ]);
        echo "✓ Created configuration: {$config['name']}\n";
        
        // Добавляем компоненты в конфигурацию
        foreach ($config['components'] as $compId => $qty) {
            $detail->componentsInConfiguration()->attach($compId, ['quantity' => $qty]);
        }
        echo "  └─ Added {" . count($config['components']) . "} components\n";
    }
}

echo "\n✅ Test data created successfully!\n";
echo "Now you can visit: http://localhost:8000/production-planning\n";
