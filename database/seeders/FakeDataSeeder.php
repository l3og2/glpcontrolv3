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
        $allPrices = Price::all()->groupBy('state_id');

        $progressBar = $this->command->getOutput()->createProgressBar(1000);

        for ($i = 0; $i < 1000; $i++) {
            $analyst = $analysts->random();
            $stateId = $analyst->state_id;
            
            $date = Carbon::instance(fake()->dateTimeBetween('-6 months', 'now'));
            $type = fake()->randomElement(['entrada', 'salida']);

            $data = [
                'user_id' => $analyst->id,
                'state_id' => $stateId,
                'status' => fake()->randomElement(['aprobado', 'revisado', 'ingresado']),
                'type' => $type,
                'movement_date' => $date,
                'volume_liters' => fake()->randomFloat(2, 50, 38000),
                'batch_id' => Str::uuid(), // Asignamos un ID de lote único a cada movimiento
                'control_number' => $controlNumberService->generateForState($stateId, $date),
            ];

            if ($type === 'entrada') {
                $stateTanks = $tanks->where('state_id', $stateId);
                if ($stateTanks->isEmpty()) continue;

                $data = array_merge($data, [
                    'tank_id' => $stateTanks->random()->id,
                    'supply_source' => fake()->randomElement(['Criogénico de Jose', 'Refinería El Palito', 'Refinería Amuay']),
                    'pdvsa_sale_number' => fake()->numerify('ORD-######'),
                    'driver_name' => fake()->name(),
                    'driver_ci' => fake()->numerify('V-########'),
                    'chuto_plate' => fake()->bothify('A##??##'),
                    'cisterna_plate' => fake()->bothify('R##??##'),
                    'cisterna_capacity_gallons' => 10200,
                ]);
            } else { // type === 'salida'
                $product = $products->random();
                $stateClients = $clients->where('state_id', $stateId);
                $statePrices = $allPrices->get($stateId);
                $price = $statePrices ? $statePrices->firstWhere('product_id', $product->id) : null;
                
                $unitPrice = $price ? $price->price : 0;
                $totalAmount = $data['volume_liters'] * $unitPrice;

                $data = array_merge($data, [
                    'product_id' => $product->id,
                    'client_id' => $stateClients->isNotEmpty() ? $stateClients->random()->id : null,
                    'unit_price' => $unitPrice,
                    'total_amount' => $totalAmount,
                ]);
            }

            InventoryMovement::create($data);
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->command->info("\n¡Datos de prueba generados exitosamente!");
    }
}