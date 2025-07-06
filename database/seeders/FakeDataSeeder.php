<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\State;
use App\Models\Role;
use App\Models\Product;
use App\Models\Tank;
use App\Models\Client;
use App\Models\InventoryMovement;
use App\Services\ControlNumberService;
use Carbon\Carbon;

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
            // Crear un Gerente por estado
            User::factory()->create([
                'name' => 'Gerente ' . $state->name,
                'email' => 'gerente.' . strtolower($state->code) . '@gascomunal.com.ve',
                'state_id' => $state->id,
            ])->assignRole('Gerente Regional');

            // Crear dos Supervisores por estado
            User::factory()->count(2)->create([
                'name' => fn () => 'Supervisor ' . $state->name . ' ' . fake()->lastName(),
                'email' => fn () => fake()->unique()->safeEmail(),
                'state_id' => $state->id,
            ])->each(function ($user) {
                $user->assignRole('Supervisor');
            });
            
            // Crear cinco Analistas por estado
            User::factory()->count(5)->create([
                'name' => fn () => 'Analista ' . $state->name . ' ' . fake()->lastName(),
                'email' => fn () => fake()->unique()->safeEmail(),
                'state_id' => $state->id,
            ])->each(function ($user) {
                $user->assignRole('Analista');
            });
        }
        $this->command->info('Usuarios de prueba creados.');

        // --- 2. GENERAR CLIENTES DE PRUEBA ---
        $this->command->info('Creando clientes de prueba...');
        Client::factory()->count(50)->create();
        $this->command->info('Clientes de prueba creados.');

        // --- 3. GENERAR MOVIMIENTOS DE INVENTARIO ---
        $this->command->info('Generando 1000 movimientos de inventario aleatorios...');

        $analysts = User::role('Analista')->get();
        $products = Product::all();
        $tanks = Tank::all();
        $clients = Client::all();
        $controlNumberService = new ControlNumberService();

        // Usamos una barra de progreso para una mejor experiencia en la consola
        $progressBar = $this->command->getOutput()->createProgressBar(1000);

        for ($i = 0; $i < 1000; $i++) {
            $analyst = $analysts->random();
            $product = $products->random();
            
            // Aseguramos que el tanque y el cliente pertenezcan al mismo estado que el analista
            $stateTanks = $tanks->where('state_id', $analyst->state_id);
            $stateClients = $clients->where('state_id', $analyst->state_id);
            
            // Si no hay tanques o clientes para ese estado, saltamos la iteración
            if ($stateTanks->isEmpty()) continue;

            $tank = $stateTanks->random();
            $client = $stateClients->isNotEmpty() ? $stateClients->random() : null;

            // Fecha aleatoria en los últimos 6 meses
            $date = Carbon::instance(fake()->dateTimeBetween('-6 months', 'now'));

            // Decidimos aleatoriamente si es entrada o salida
            $type = fake()->randomElement(['entrada', 'salida']);

            InventoryMovement::create([
                // Datos del Sistema
                'user_id' => $analyst->id,
                'state_id' => $analyst->state_id,
                'control_number' => $controlNumberService->generateForState($analyst->state_id, $date),
                'status' => fake()->randomElement(['aprobado', 'revisado', 'ingresado']), // Estados variados
                'type' => $type,
                'movement_date' => $date,

                // Datos de la Transacción
                'volume_liters' => fake()->randomFloat(2, 50, 38000), // Volumen entre 50 y 38,000 litros
                'product_id' => ($type === 'salida') ? $product->id : null,
                'tank_id' => ($type === 'entrada') ? $tank->id : null,
                'client_id' => ($type === 'salida' && $client) ? $client->id : null,

                // Detalles de la Orden de Llenado (solo para entradas)
                'supply_source' => ($type === 'entrada') ? fake()->randomElement(['Criogénico de Jose', 'Refinería El Palito', 'Refinería Amuay']) : null,
                'pdvsa_sale_number' => ($type === 'entrada') ? fake()->numerify('ORD-######') : null,
                'chuto_plate' => ($type === 'entrada') ? fake()->bothify('A##??##') : null,
                'cisterna_plate' => ($type === 'entrada') ? fake()->bothify('R##??##') : null,
                'cisterna_capacity_gallons' => ($type === 'entrada') ? 10200 : null,
                'driver_name' => ($type === 'entrada') ? fake()->name() : null,
                'driver_ci' => ($type === 'entrada') ? fake()->numerify('V-########') : null,
                // ... puedes añadir más campos falsos aquí si lo deseas
            ]);
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->command->info("\n¡Datos de prueba generados exitosamente!");
    }
}