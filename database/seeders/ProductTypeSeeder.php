<?php

namespace Database\Seeders;

use App\Models\ProductType;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductType::firstOrCreate(['name' => 'Готовое изделие']);
        ProductType::firstOrCreate(['name' => 'Комплектующие']);
        ProductType::firstOrCreate(['name' => 'Детали']);
    }
}
