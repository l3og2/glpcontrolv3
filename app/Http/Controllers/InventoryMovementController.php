<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{State, Product, Client, Tank, InventoryMovement};
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMovementRequest $request, ControlNumberService $controlNumberService)
    {
        // 1. La validación ya se ejecutó gracias al Form Request.
    
        // 2. Generamos el número de control.
        $controlNumber = $controlNumberService->generateForState(Auth::user()->state_id);

        // 3. Creamos el movimiento en la base de datos.
        InventoryMovement::create([
        'user_id' => Auth::id(),
        'state_id' => Auth::user()->state_id,
        'control_number' => $controlNumber,
        'status' => 'ingresado', // Estado inicial del workflow
        // El resto de los datos vienen del request validado:
        'type' => $request->type,
        'movement_date' => $request->movement_date,
        'volume_liters' => $request->volume_liters,
        'tank_id' => $request->tank_id,
        'product_id' => $request->product_id,
        'client_id' => $request->client_id,
        'notes' => $request->notes,
    ]);

    // 4. Redirigimos con un mensaje de éxito.
    return redirect()->route('movements.index')
        ->with('success', "Movimiento registrado con el N° de Control: $controlNumber");
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
