<?php
// Тест исправления связей моделей после переименования таблиц

// Инициализируем Laravel
require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;

$kernel = app(Kernel::class);
$kernel->bootstrap();

use App\Models\Detail;
use App\Models\Place;

echo "\n=== ТЕСТ ИСПРАВЛЕНИЯ СВЯЗЕЙ МОДЕЛЕЙ ===\n\n";

try {
    echo "1. Проверка Detail модели...\n";
    $detail = Detail::first();
    if ($detail) {
        echo "   ✓ Detail найден: ID=" . $detail->id . ", SCU=" . $detail->scu . "\n";
    } else {
        echo "   ⚠ No details found\n";
    }

    echo "\n2. Проверка Place модели...\n";
    $place = Place::first();
    if ($place) {
        echo "   ✓ Place найден: ID=" . $place->id . "\n";
    } else {
        echo "   ⚠ No places found\n";
    }

    echo "\n3. Проверка связи Place -> details...\n";
    if ($place) {
        $count = $place->details()->count();
        echo "   ✓ Количество связанных деталей: " . $count . "\n";
        
        $details = $place->details()->get();
        if ($details->count() > 0) {
            echo "   ✓ Первая деталь: " . $details->first()->name . "\n";
        }
    }

    echo "\n4. Проверка связи Detail -> places (обратная)...\n";
    if ($detail) {
        $count = $detail->places()->count();
        echo "   ✓ Количество мест для детали: " . $count . "\n";
    }

    echo "\n✅ ВСЕ ПРОВЕРКИ ПРОШЛИ УСПЕШНО!\n\n";

} catch (\Exception $e) {
    echo "\n❌ ОШИБКА: " . $e->getMessage() . "\n";
    echo "   Файл: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo "Trace:\n";
    echo $e->getTraceAsString() . "\n\n";
}
