<?php
/**
 * Проверка связей моделей после переименования таблиц
 * Запуск: php artisan tinker < test_models.php
 */

use App\Models\Detail;
use App\Models\Place;
use App\Models\Cabinet;

echo "\n=== ПРОВЕРКА МОДЕЛЕЙ ПОСЛЕ ПЕРЕИМЕНОВАНИЯ ТАБЛИЦ ===\n\n";

// 1. Проверим что Detail модель указывает на products
echo "1. Проверка Detail модели (products):\n";
$detail = Detail::first();
if ($detail) {
    echo "   ✓ Detail найден: ID=" . $detail->id . "\n";
} else {
    echo "   ⚠ Деталей нет в БД\n";
}

// 2. Проверим что Place модель работает
echo "\n2. Проверка Place модели:\n";
$place = Place::first();
if ($place) {
    echo "   ✓ Place найден: ID=" . $place->id . "\n";
} else {
    echo "   ⚠ Мест нет в БД\n";
}

// 3. Проверим связь Place -> details (через configs)
echo "\n3. Проверка связи Place->details (через configs):\n";
if ($place) {
    try {
        $count = $place->details()->count();
        echo "   ✓ Связь Place->details работает\n";
        echo "   ✓ Количество связанных деталей: " . $count . "\n";
    } catch (\Exception $e) {
        echo "   ✗ ОШИБКА: " . $e->getMessage() . "\n";
    }
}

// 4. Проверим связь Detail -> places
echo "\n4. Проверка связи Detail->places (через configs):\n";
if ($detail) {
    try {
        $count = $detail->places()->count();
        echo "   ✓ Связь Detail->places работает\n";
        echo "   ✓ Количество мест: " . $count . "\n";
    } catch (\Exception $e) {
        echo "   ✗ ОШИБКА: " . $e->getMessage() . "\n";
    }
}

echo "\n✅ ПРОВЕРКА ЗАВЕРШЕНА\n\n";
