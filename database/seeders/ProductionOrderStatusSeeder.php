<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductionOrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\ProductionOrderStatus::upsert([
            [
                'code' => 'ordering',
                'name' => 'Ожидает',
                'color' => '#fbbf24',
                'description' => 'Заказ создан и ожидает начала производства',
                'sort_order' => 1,
            ],
            [
                'code' => 'in_production',
                'name' => 'В производстве',
                'color' => '#60a5fa',
                'description' => 'Заказ находится в процессе производства',
                'sort_order' => 2,
            ],
            [
                'code' => 'ready',
                'name' => 'Готов',
                'color' => '#34d399',
                'description' => 'Заказ завершён и готов к отправке',
                'sort_order' => 3,
            ],
            [
                'code' => 'completed',
                'name' => 'Завершен',
                'color' => '#a78bfa',
                'description' => 'Заказ получен клиентом',
                'sort_order' => 4,
            ],
        ], ['code']);
    }
}
