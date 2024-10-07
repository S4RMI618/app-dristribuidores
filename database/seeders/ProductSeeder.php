<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Crear 4 productos aleatorios
        for ($i = 0; $i < 4; $i++) {
            Product::create([
                'name' => $faker->word(),
                'code' => strtoupper($faker->lexify('???-???')),
                'base_price' => $faker->randomFloat(2, 10000, 100000), // Precio base entre 10000 y 100000
                'tax_rate' => $faker->randomFloat(2, 0, 0.19), // Tasa de impuesto entre 0% y 25%
                'company_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
