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

        $products = [
            // Residencial
            ['name' => 'Cilindros 10KG-Res', 'type' => 'cilindro', 'unit_of_measure' => 'kg'],
            ['name' => 'Cilindros 18KG-Res', 'type' => 'cilindro', 'unit_of_measure' => 'kg'],
            ['name' => 'Cilindros 27KG-Res', 'type' => 'cilindro', 'unit_of_measure' => 'kg'],
            ['name' => 'Cilindros 43KG-Res', 'type' => 'cilindro', 'unit_of_measure' => 'kg'],
            ['name' => 'Granel-Res Litros', 'type' => 'granel', 'unit_of_measure' => 'litro'],
            // Comercial
            ['name' => 'Cilindros 10KG-Com', 'type' => 'cilindro', 'unit_of_measure' => 'kg'],
            ['name' => 'Cilindros 18KG-Com', 'type' => 'cilindro', 'unit_of_measure' => 'kg'],
            ['name' => 'Cilindros 27KG-Com', 'type' => 'cilindro', 'unit_of_measure' => 'kg'],
            ['name' => 'Cilindros 43KG-Com', 'type' => 'cilindro', 'unit_of_measure' => 'kg'],
            ['name' => 'Granel-Com Litros', 'type' => 'granel', 'unit_of_measure' => 'litro'],
            // Industrial
            ['name' => 'Carburación-Ind Litros', 'type' => 'carburacion', 'unit_of_measure' => 'litro'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}