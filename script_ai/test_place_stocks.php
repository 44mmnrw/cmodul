<?php
/**
 * Скрипт для тестирования системы остатков мест
 * Использование: php artisan tinker < script_ai/test_place_stocks.php
 */

use App\Models\Place;
use App\Models\PlaceStock;
use App\Models\PlaceStockMovement;
use App\Models\PlaceStockSnapshot;

echo "=== Тестирование системы остатков мест ===\n";

// Получаем первое место или создаём новое
$place = Place::first() ?? Place::create([
    'place_id' => 'TEST-001',
    'name' => 'Тестовое место',
]);

echo "\n📍 Место: {$place->name} ({$place->place_id})\n";

// Тест 1: Добавляем приход
echo "\n✅ Добавляем приход: +50 шт (Поступление #001)\n";
PlaceStock::addStock($place->id, 50, 'PO-001', 'Поступление со склада');
$quantity = PlaceStock::getQuantity($place->id);
echo "   Текущий остаток: {$quantity} шт\n";

// Тест 2: Добавляем ещё приход
echo "\n✅ Добавляем приход: +30 шт (Поступление #002)\n";
PlaceStock::addStock($place->id, 30, 'PO-002', 'Поступление со склада');
$quantity = PlaceStock::getQuantity($place->id);
echo "   Текущий остаток: {$quantity} шт\n";

// Тест 3: Расход
echo "\n❌ Добавляем расход: -15 шт (Отпуск #001)\n";
PlaceStock::removeStock($place->id, 15, 'SO-001', 'Отпуск на монтаж');
$quantity = PlaceStock::getQuantity($place->id);
echo "   Текущий остаток: {$quantity} шт\n";

// Тест 4: Вывод истории движений
echo "\n📋 История движений:\n";
$movements = PlaceStockMovement::where('place_id', $place->id)->orderBy('created_at')->get();
foreach ($movements as $movement) {
    $type = $movement->type === 'incoming' ? '➕' : '➖';
    echo "   {$type} {$movement->movement_date} | {$movement->quantity} шт | {$movement->reference} | {$movement->description}\n";
}

// Тест 5: Статистика
echo "\n📊 Статистика приходов/расходов:\n";
$totalIncoming = PlaceStockMovement::where('place_id', $place->id)
    ->where('type', 'incoming')
    ->sum('quantity');
$totalOutgoing = PlaceStockMovement::where('place_id', $place->id)
    ->where('type', 'outgoing')
    ->sum('quantity');
echo "   Всего приходов: {$totalIncoming} шт\n";
echo "   Всего расходов: {$totalOutgoing} шт\n";
echo "   Текущий остаток: {$quantity} шт\n";

// Тест 6: Создание снимка
echo "\n📸 Создаём снимок состояния на текущую дату\n";
PlaceStockSnapshot::createSnapshot($place->id);
$snapshot = PlaceStockSnapshot::where('place_id', $place->id)->latest('snapshot_date')->first();
echo "   Снимок создан: {$snapshot->snapshot_date} | Остаток: {$snapshot->quantity} шт\n";

echo "\n✨ Тестирование завершено!\n";
