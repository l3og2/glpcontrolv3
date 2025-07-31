<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Price;
use App\Models\Product;
use App\Models\State;

class PriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiamos la tabla para empezar de cero
        Price::truncate();

        $this->command->info('Iniciando el seeder de precios para todos los estados...');
        
        // Obtenemos todos los estados y productos una sola vez para eficiencia
        $states = State::all();
        $products = Product::all();

        // Tarifas base (usando las de Miranda como referencia)
        $baseTariffs = [
            'Cilindros 10KG' => 5.00,
            'Cilindros 18KG' => 9.00,
            'Cilindros 27KG' => 13.50,
            'Cilindros 43KG' => 21.50,
            'Granel Litro' => 0.40,
            'Carburación Litro' => 0.45,
        ];
        
        // Creamos una barra de progreso para una mejor experiencia visual
        $progressBar = $this->command->getOutput()->createProgressBar($states->count() * $products->count());

        // Recorremos cada estado
        foreach ($states as $state) {
            // Recorremos cada producto
            foreach ($products as $product) {
                // Limpiamos el nombre del producto para encontrar su tarifa base
                $baseProductName = trim(str_replace(['-Res', '-Com', '-Conv'], '', $product->name));
                $basePrice = $baseTariffs[$baseProductName] ?? 0; // Usamos 0 si no se encuentra
                
                // Aplicamos un modificador de precio según la categoría (Residencial, Comercial, Convenios)
                $priceModifier = 1.0;
                if (str_contains($product->name, '-Com')) $priceModifier = 1.30; // Comercial es 30% más caro
                if (str_contains($product->name, '-Conv')) $priceModifier = 0.77; // Convenios es 23% más barato
                
                // Añadimos una pequeña variación aleatoria (+/- 5%) para simular diferencias entre estados
                $randomVariation = 1 + (rand(-5, 5) / 100);
                
                // Calculamos el precio final
                $finalPrice = ($basePrice * $priceModifier) * $randomVariation;

                Price::create([
                    'product_id' => $product->id,
                    'state_id' => $state->id,
                    'price' => $finalPrice,
                ]);
                
                $progressBar->advance();
            }
        }
        
        $progressBar->finish();
        $this->command->info("\n¡Precios generados exitosamente para los 24 estados!");
    }
}