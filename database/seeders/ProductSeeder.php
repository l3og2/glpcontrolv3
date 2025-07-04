<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::truncate(); // Limpia la tabla para evitar duplicados

        $baseProducts = [
    ['name' => 'Cilindros 10KG', 'type' => 'cilindro', 'unit_of_measure' => 'kg', 'weight_kg' => 10, 'volume_liters' => 19.72],
    ['name' => 'Cilindros 18KG', 'type' => 'cilindro', 'unit_of_measure' => 'kg', 'weight_kg' => 18, 'volume_liters' => 35.50],
    ['name' => 'Cilindros 27KG', 'type' => 'cilindro', 'unit_of_measure' => 'kg', 'weight_kg' => 27, 'volume_liters' => 53.25],
    ['name' => 'Cilindros 43KG', 'type' => 'cilindro', 'unit_of_measure' => 'kg', 'weight_kg' => 43, 'volume_liters' => 84.81],
    ['name' => 'Granel Litro', 'type' => 'granel', 'unit_of_measure' => 'litro', 'weight_kg' => null, 'volume_liters' => 1.00],
    ['name' => 'Carburación Litro', 'type' => 'carburacion', 'unit_of_measure' => 'litro', 'weight_kg' => null, 'volume_liters' => 1.00],
];

$categories = ['-Res', '-Com', '-Conv']; // -Conv para Convenios

foreach ($categories as $category) {
    foreach ($baseProducts as $baseProduct) {
        Product::create([
            'name' => $baseProduct['name'] . $category,
            'type' => $baseProduct['type'],
            'unit_of_measure' => $baseProduct['unit_of_measure'],
            'weight_kg' => $baseProduct['weight_kg'],
            'volume_liters' => $baseProduct['volume_liters'],
        ]);
    }
    }
    }  
}