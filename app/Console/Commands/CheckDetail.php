<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Detail;

class CheckDetail extends Command
{
    protected $signature = 'check:detail {name}';
    protected $description = 'Проверить деталь';

    public function handle()
    {
        $name = $this->argument('name');
        
        $detail = Detail::where('name', 'like', '%' . $name . '%')->first();
        
        if (!$detail) {
            $this->error("Деталь не найдена: $name");
            return 1;
        }

        $this->line("\n=== ДЕТАЛЬ: {$detail->name} ===");
        $this->line("ID: {$detail->id}");
        $this->line("SCU: {$detail->scu}");
        
        // Проверим places (связь Detail -> Place через configs как slave)
        $this->line("\n📍 МЕСТА ИСПОЛЬЗОВАНИЯ (через configs как master_id -> slave_id):");
        $places = $detail->places()->get();
        $this->line("   Всего: " . count($places));
        foreach ($places as $p) {
            $this->line("   - {$p->name} (ID={$p->id}, place_id={$p->place_id})");
        }
        
        // Проверим componentsInConfiguration (связь Detail -> Detail как master)
        $this->line("\n🔧 КОМПОНЕНТЫ В КОНФИГУРАЦИИ (через configs как slave_id):");
        $components = $detail->componentsInConfiguration()->get();
        $this->line("   Всего: " . count($components));
        foreach ($components as $c) {
            $this->line("   - {$c->name} (ID={$c->id})");
        }
        
        // Проверим usedInCabinets (связь Detail -> Detail как slave)
        $this->line("\n📦 ИСПОЛЬЗУЕТСЯ В КАБИНЕТАХ (через configs как slave_id -> master_id):");
        $cabinets = $detail->usedInCabinets()->get();
        $this->line("   Всего: " . count($cabinets));
        foreach ($cabinets as $c) {
            $this->line("   - {$c->name} (ID={$c->id})");
        }
        
        // Прямой запрос в БД
        $this->line("\n🔍 ПРОВЕРКА CONFIGS ТАБЛИЦЫ:");
        $configs1 = \DB::table('configs')
            ->where('master_id', $detail->id)
            ->get();
        $this->line("   Записи где master_id = {$detail->id}: " . count($configs1));
        foreach ($configs1 as $c) {
            $this->line("      - master_id={$c->master_id}, slave_id={$c->slave_id}, qty={$c->quantity}");
        }
        
        $configs2 = \DB::table('configs')
            ->where('slave_id', $detail->id)
            ->get();
        $this->line("   Записи где slave_id = {$detail->id}: " . count($configs2));
        foreach ($configs2 as $c) {
            $this->line("      - master_id={$c->master_id}, slave_id={$c->slave_id}, qty={$c->quantity}");
        }
        
        return 0;
    }
}
