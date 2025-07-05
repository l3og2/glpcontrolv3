<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Price;
use App\Models\Product;
use App\Models\State;

class PriceSeeder extends Seeder
{
    public function run(): void
    {
        Price::truncate();

        // Vamos a crear precios para el estado Miranda como ejemplo
        $miranda = State::where('code', 'MIR')->first();
        if (!$miranda) {
            $this->command->info('Estado Miranda no encontrado, saltando PriceSeeder.');
            return;
        }

        // Definimos las tarifas de la hoja de cálculo
        $tariffs = [
            // Residencial
            'Cilindros 10KG-Res' => 5.00,
            'Cilindros 18KG-Res' => 9.00,
            'Cilindros 27KG-Res' => 13.50,
            'Cilindros 43KG-Res' => 21.50,
            'Granel Litro-Res' => 0.40,
            'Carburación Litro-Res' => 0.45,
            // Comercial
            'Cilindros 10KG-Com' => 6.50,
            'Cilindros 18KG-Com' => 11.70,
            'Cilindros 27KG-Com' => 17.55,
            'Cilindros 43KG-Com' => 27.95,
            'Granel Litro-Com' => 0.52,
            'Carburación Litro-Com' => 0.59,
            // Convenios
            'Cilindros 10KG-Conv' => 3.85,
            'Cilindros 18KG-Conv' => 6.92,
            'Cilindros 27KG-Conv' => 10.38,
            'Cilindros 43KG-Conv' => 16.54,
            'Granel Litro-Conv' => 0.31,
            'Carburación Litro-Conv' => 0.35,
        ];

        foreach ($tariffs as $productName => $price) {
            // Buscamos el producto por su nombre completo
            $product = Product::where('name', $productName)->first();

            if ($product) {
                Price::create([
                    'product_id' => $product->id,
                    'state_id' => $miranda->id,
                    'price' => $price,
                ]);
            } else {
                 $this->command->warn("Producto no encontrado para la tarifa: $productName");
            }
        }
    }
}