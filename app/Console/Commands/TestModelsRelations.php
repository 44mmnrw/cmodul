<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Detail;
use App\Models\Place;

class TestModelsRelations extends Command
{
    protected $signature = 'test:models';
    protected $description = 'Проверка связей моделей после переименования таблиц';

    public function handle()
    {
        $this->info('=== ПРОВЕРКА МОДЕЛЕЙ ПОСЛЕ ПЕРЕИМЕНОВАНИЯ ТАБЛИЦ ===');
        
        // 1. Проверим Detail
        $this->line("\n1. Проверка Detail модели (products):");
        $detail = Detail::first();
        if ($detail) {
            $this->line("   ✓ Detail найден: ID=" . $detail->id);
        } else {
            $this->warn("   ⚠ Деталей нет в БД");
        }

        // 2. Проверим Place
        $this->line("\n2. Проверка Place модели:");
        $place = Place::first();
        if ($place) {
            $this->line("   ✓ Place найден: ID=" . $place->id);
        } else {
            $this->warn("   ⚠ Мест нет в БД");
        }

        // 3. Проверим связь Place -> details
        $this->line("\n3. Проверка связи Place->details (через configs):");
        if ($place) {
            try {
                $count = $place->details()->count();
                $this->line("   ✓ Связь Place->details работает");
                $this->line("   ✓ Количество связанных деталей: " . $count);
            } catch (\Exception $e) {
                $this->error("   ✗ ОШИБКА: " . $e->getMessage());
            }
        }

        // 4. Проверим обратную связь
        $this->line("\n4. Проверка связи Detail->places (через configs):");
        if ($detail) {
            try {
                $count = $detail->places()->count();
                $this->line("   ✓ Связь Detail->places работает");
                $this->line("   ✓ Количество мест: " . $count);
            } catch (\Exception $e) {
                $this->error("   ✗ ОШИБКА: " . $e->getMessage());
            }
        }

        // 5. Проверим configs таблицу
        $this->line("\n5. Проверка configs таблицы:");
        $configsCount = \DB::table('configs')->count();
        $this->line("   ✓ Записей в configs: " . $configsCount);

        $this->info("\n✅ ПРОВЕРКА ЗАВЕРШЕНА\n");
        return 0;
    }
}
