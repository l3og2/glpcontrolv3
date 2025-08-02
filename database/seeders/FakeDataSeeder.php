<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\State;
use App\Models\Product;
use App\Models\Tank;
use App\Models\Client;
use App\Models\Price;
use App\Models\InventoryMovement;
use App\Services\ControlNumberService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class FakeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Iniciando el seeder de datos de prueba (FakeDataSeeder)...');

        // --- 1. GENERAR USUARIOS DE PRUEBA ---
        $this->command->info('Creando usuarios de prueba para cada estado...');
        $states = State::all();
        
        foreach ($states as $state) {
            User::factory()->create([
                'name' => 'Gerente ' . $state->name,
                'email' => 'gerente.' . strtolower($state->code) . '@gascomunal.com.ve',
                'state_id' => $state->id,
            ])->assignRole('Gerente Regional');

            User::factory()->count(2)->create([
                'name' => fn () => 'Supervisor ' . $state->name . ' ' . fake()->lastName(),
                'email' => fn () => fake()->unique()->safeEmail(),
                'state_id' => $state->id,
            ])->each(fn ($user) => $user->assignRole('Supervisor'));
            
            User::factory()->count(5)->create([
                'name' => fn () => 'Analista ' . $state->name . ' ' . fake()->lastName(),
                'email' => fn () => fake()->unique()->safeEmail(),
                'state_id' => $state->id,
            ])->each(fn ($user) => $user->assignRole('Analista'));
        }
        $this->command->info('Usuarios de prueba creados.');

        // --- 2. GENERAR CLIENTES DE PRUEBA ---
        $this->command->info('Creando clientes de prueba...');
        Client::factory()->count(150)->create();
        $this->command->info('Clientes de prueba creados.');

        // --- 3. GENERAR MOVIMIENTOS DE INVENTARIO ---
        $this->command->info('Generando 1000 movimientos de inventario aleatorios...');

        $analysts = User::role('Analista')->get();
        $products = Product::all();
        $tanks = Tank::all();
        $clients = Client::all();
        $controlNumberService = new ControlNumberService();
        $allPrices = Price::all()->groupBy('state_id'); // Agrupamos precios por state_id para una búsqueda eficiente

        $progressBar = $this->command->getOutput()->createProgressBar(1000);

        for ($i = 0; $i < 1000; $i++) {
            $analyst = $analysts->random();
            $stateId = $analyst->state_id;
            
            $date = Carbon::instance(fake()->dateTimeBetween('-6 months', 'now'));
            $type = fake()->randomElement(['entrada', 'salida']);

            // Datos comunes a ambos tipos de movimiento
            $data = [
                'user_id' => $analyst->id,
                'state_id' => $stateId,
                'status' => fake()->randomElement(['aprobado', 'revisado', 'ingresado']),
                'type' => $type,
                'movement_date' => $date,
                'batch_id' => Str::uuid(), // Asignamos un ID de lote único a cada movimiento
                'control_number' => $controlNumberService->generateForState($stateId, $date),
            ];

            if ($type === 'entrada') {
                $stateTanks = $tanks->where('state_id', $stateId);
                if ($stateTanks->isEmpty()) continue; // Salta si no hay tanques para el estado

                $data = array_merge($data, [
                    'tank_id' => $stateTanks->random()->id,
                    'volume_liters' => fake()->randomFloat(2, 5000, 38000), // Volumen grande para entradas
                    'supply_source' => fake()->randomElement(['Criogénico de Jose', 'Refinería El Palito', 'Refinería Amuay']),
                    'pdvsa_sale_number' => fake()->numerify('ORD-######'),
                    'driver_name' => fake()->name(),
                    'driver_ci' => fake()->numerify('V-########'),
                    'chuto_plate' => fake()->bothify('A##??##'),
                    'cisterna_plate' => fake()->bothify('R##??##'),
                    'cisterna_capacity_gallons' => 10200,
                    // Estos campos no aplican a entradas, pero los seteamos para evitar nulls innecesarios en la DB si la columna es nullable
                    'quantity' => null, 
                    'product_id' => null, 
                    'client_id' => null, 
                    'unit_price' => null, 
                    'total_amount' => null,
                ]);
            } else { // type === 'salida'
                $product = $products->random();
                $stateClients = $clients->where('state_id', $stateId);

                // --- Generación de Quantity y Volumen para Salidas ---
                $quantity = fake()->numberBetween(1, 100); // Cantidad de unidades vendidas
                $volumeInLiters = ($product->unit_of_measure === 'kg') ? $quantity * $product->volume_liters : $quantity;
                
                // Buscar precio y calcular monto total
                $statePrices = $allPrices->get($stateId); // Precios para el estado actual
                $price = $statePrices ? $statePrices->firstWhere('product_id', $product->id) : null;
                
                $unitPrice = $price ? $price->price : 0;
                $totalAmount = $volumeInLiters * $unitPrice; // Usamos volumeInLiters para el cálculo total

                $data = array_merge($data, [
                    'product_id' => $product->id,
                    'client_id' => $stateClients->isNotEmpty() ? $stateClients->random()->id : null,
                    'volume_liters' => $volumeInLiters, // Volumen en litros calculado
                    'quantity' => $quantity, // Cantidad de unidades vendidas
                    'unit_price' => $unitPrice,
                    'total_amount' => $totalAmount,
                    // Estos campos no aplican a salidas
                    'tank_id' => null, 'supply_source' => null, 'pdvsa_sale_number' => null, 
                    'driver_name' => null, 'driver_ci' => null, 'chuto_plate' => null, 
                    'cisterna_plate' => null, 'cisterna_capacity_gallons' => null,
                ]);
            }

            InventoryMovement::create($data);
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->command->info("\n¡Datos de prueba generados exitosamente!");
    }
}