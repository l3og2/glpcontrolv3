<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{State, Product, Client, Tank, InventoryMovement};
use App\Models\Price;
use App\Services\ControlNumberService;
use App\Http\Requests\StoreMovementRequest;
use Illuminate\Support\Facades\Auth;

class InventoryMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //  --- 1. OBTENER LOS DATOS ---
    // Obtenemos los movimientos ordenados por el más reciente.
    // El método with() es para "Eager Loading": carga las relaciones
    // (user, product, tank) en una sola consulta, lo que es MUY eficiente.
    // El método paginate() divide los resultados en páginas.
    $movements = InventoryMovement::with('user', 'product', 'tank')
                                ->latest() // Ordena por 'created_at' descendente
                                ->paginate(15); // Muestra 15 registros por página

    // --- 2. DEVOLVER LA VISTA CON LOS DATOS ---
    // Pasamos la colección (aunque esté vacía) a la vista.
    return view('movements.index', compact('movements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtenemos solo los datos relevantes para el estado del usuario logueado
        $userStateId = Auth::user()->state_id;

        $tanks = Tank::where('state_id', $userStateId)->get();
        $clients = Client::where('state_id', $userStateId)->get();
        $products = Product::all(); // Los productos pueden ser para cualquier estado

        return view('movements.create', compact('tanks', 'clients', 'products'));
    }

    public function createBatchSalida()
    {
    // Obtenemos todos los productos.
    $products = Product::all();

    // Obtenemos los precios solo para el estado del usuario actual para optimizar.
    // Usamos pluck() para crear un array asociativo [product_id => price], muy eficiente.
    $prices = Price::where('state_id', Auth::user()->state_id)
                   ->pluck('price', 'product_id');

    // Devolvemos la nueva vista y le pasamos los productos y el array de precios.
    return view('movements.create_batch_salida', compact('products', 'prices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMovementRequest $request, ControlNumberService $controlNumberService)
    {
        
        // --- 1. VALIDACIÓN ---    
        // La validación ya se ejecutó automáticamente gracias a StoreMovementRequest.
        // Solo los datos que pasaron la validación están disponibles aquí.

        // 1. Generamos el número de control para el estado del usuario logueado.
        $controlNumber = $controlNumberService->generateForState(Auth::user()->state_id);
        
        // 2. Usamos el método `create` con todos los campos del formulario y los generados por el sistema.
        // Nos aseguramos de que todos estos campos estén en el array `$fillable` del modelo InventoryMovement.
        InventoryMovement::create([
        // --- Datos del Sistema ---
        'user_id' => Auth::id(),
        'state_id' => Auth::user()->state_id,
        'control_number' => $controlNumber,
        'status' => 'ingresado', // Estado inicial del workflow
        'type' => 'entrada', // Lo definimos explícitamente para este formulario

        'movement_date' => $request->movement_date,

        // --- Datos del Formulario (validados) ---
        'supply_source' => $request->supply_source,
        'pdvsa_sale_number' => $request->pdvsa_sale_number,
        
        'chuto_code' => $request->chuto_code,
        'chuto_plate' => $request->chuto_plate,
        
        'cisterna_code' => $request->cisterna_code,
        'cisterna_capacity_gallons' => $request->cisterna_capacity_gallons,
        'cisterna_plate' => $request->cisterna_plate,
        'cisterna_serial' => $request->cisterna_serial,

        'driver_name' => $request->driver_name,
        'driver_ci' => $request->driver_ci,
        'driver_code' => $request->driver_code,

        'tank_id' => $request->tank_id, // Tanque de destino

        'arrival_volume_percentage' => $request->arrival_volume_percentage,
        'arrival_temperature' => $request->arrival_temperature,
        'arrival_pressure' => $request->arrival_pressure,
        'arrival_specific_gravity' => $request->arrival_specific_gravity,

        'departure_volume_percentage' => $request->departure_volume_percentage,
        'departure_temperature' => $request->departure_temperature,
        'departure_pressure' => $request->departure_pressure,
        'departure_specific_gravity' => $request->departure_specific_gravity,

        'volume_liters' => $request->volume_liters, // Litros Netos Despachados
        
        // El campo 'notes' no lo definimos en el formulario, pero lo dejamos por si se añade en el futuro
        'notes' => $request->notes,
        ]);

        // 4. Redirigimos con un mensaje de éxito.
        return redirect()->route('movements.index')
        ->with('success', "Movimiento registrado con el N° de Control: $controlNumber");
    }

    public function storeBatchSalida(Request $request, ControlNumberService $controlNumberService)
    {
    // 1. Validación básica de los datos generales
    $request->validate([
        'movement_date' => 'required|date',
        'quantities' => 'required|array', // Nos aseguramos de que 'quantities' sea un array
    ]);

    $user = Auth::user();
    $totalVolume = 0;
    $totalSales = 0;

    // 2. Recorremos el array de cantidades que nos llega del formulario
    foreach ($request->quantities as $productId => $quantity) {
        // Solo procesamos las filas donde el analista introdujo una cantidad
        if ($quantity > 0) {
            
            // 3. Obtenemos los datos del producto y su precio
            $product = Product::find($productId);
            if (!$product) continue; // Si el producto no existe, saltamos a la siguiente iteración

            $price = Price::where('product_id', $productId)
                          ->where('state_id', $user->state_id)
                          ->first();
            
            $unitPrice = $price ? $price->price : 0;
            
            // Si la unidad de medida es por 'kg', usamos el volumen en litros pre-calculado. Si no, usamos la cantidad.
            $volumeInLiters = ($product->unit_of_measure === 'kg') ? $quantity * $product->volume_liters : $quantity;
            $totalAmount = $quantity * $unitPrice;
            
            // Acumulamos los totales
            $totalVolume += $volumeInLiters;
            $totalSales += $totalAmount;

            // 2. Generamos un nuevo número de control DENTRO de cada iteración del bucle
            $controlNumber = $controlNumberService->generateForState($user->state_id, new \Carbon\Carbon($request->movement_date));

            // 4. Creamos un registro de movimiento individual para cada producto vendido
            InventoryMovement::create([
                'user_id' => $user->id,
                'state_id' => $user->state_id,
                'control_number' => $controlNumberService->generateForState($user->state_id),
                'status' => 'ingresado',
                'type' => 'salida',
                'movement_date' => $request->movement_date,
                'product_id' => $productId,
                'volume_liters' => $volumeInLiters, // Guardamos el volumen total en litros
                'quantity' => $quantity, // Podríamos añadir una columna 'quantity' para guardar las unidades
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
            ]);
        }
    }

    // 5. Si no se vendió nada, volvemos con un aviso.
    if ($totalVolume == 0) {
        return redirect()->back()->with('warning', 'No se ingresaron cantidades. No se ha registrado ninguna venta.');
    }

    // 6. Redirigimos con un mensaje de éxito que resume la operación
    return redirect()->route('movements.index')
        ->with('success', "Reporte de ventas guardado con éxito. Total Volumen: " . number_format($totalVolume, 2) . " Lts. Total Ventas: Bs. " . number_format($totalSales, 2));
    }    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
