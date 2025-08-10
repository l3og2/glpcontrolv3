<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{State, Product, Client, Tank, InventoryMovement, Price};
use App\Services\ControlNumberService;
use App\Http\Requests\StoreMovementRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class InventoryMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // --- INICIO DE LA LÓGICA DE FILTROS ---
        $baseQuery = InventoryMovement::query();

        // Filtro por Rol: Los no-Admins solo ven su estado.
        if (!$user->hasRole('Admin')) {
            $baseQuery->where('state_id', $user->state_id);
        } 
        // Si el Admin usa el filtro de estado, se aplica.
        elseif ($user->hasRole('Admin') && $request->filled('state_id')) {
            $baseQuery->where('state_id', $request->state_id);
        }
        
        // Filtro por Estatus del movimiento (ingresado, revisado, aprobado)
        if ($request->filled('status')) {
            $baseQuery->where('status', $request->status);
        }
        // --- FIN DE LA LÓGICA DE FILTROS ---

        $perPage = 15;
        $currentPage = request()->get('page', 1);

        $salidasAgrupadas = (clone $baseQuery)
            ->where('type', 'salida')->whereNotNull('batch_id')
            ->select('batch_id', 'user_id', 'movement_date', 'status', 'type', DB::raw('NULL as tank_id'), DB::raw('SUM(volume_liters) as total_volume'), DB::raw('SUM(total_amount) as total_sales'), DB::raw('MIN(control_number) as first_control_number'), DB::raw('COUNT(*) as item_count'), DB::raw('MIN(id) as id'))
            ->groupBy('batch_id', 'user_id', 'movement_date', 'status', 'type');

        $entradasIndividuales = (clone $baseQuery)
            ->where('type', 'entrada')
            ->select('batch_id', 'user_id', 'movement_date', 'status', 'type', 'tank_id', 'volume_liters as total_volume', 'total_amount as total_sales', 'control_number as first_control_number', DB::raw('1 as item_count'), 'id');
            
        $unionedResults = $salidasAgrupadas->union($entradasIndividuales)->orderBy('movement_date', 'desc')->get();
        
        $paginatedResults = new LengthAwarePaginator(
            $unionedResults->forPage($currentPage, $perPage),
            $unionedResults->count(), $perPage, $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        
        $paginatedResults->load('user', 'tank');
        $movements = $paginatedResults;
        
        // Obtenemos los estados para el dropdown del filtro (solo necesario para el Admin)
        $states = State::orderBy('name')->get();
        
        return view('movements.index', compact('movements', 'states'));
    }
 
    /**
     * Show the form for creating a new resource (ENTRADA - Orden de Llenado).
     */
    public function create()
    {
        $userStateId = Auth::user()->state_id;
        $tanks = Tank::where('state_id', $userStateId)->get();
        return view('movements.create', compact('tanks'));
    }

    /**
     * Store a newly created resource in storage (ENTRADA).
     */
    public function store(StoreMovementRequest $request, ControlNumberService $controlNumberService)
    {
        $validatedData = $request->validated();
        $movementDate = new Carbon($validatedData['movement_date']);
        $user = Auth::user();

        $dataToSave = array_merge($validatedData, [
            'batch_id' => Str::uuid(),
            'user_id' => $user->id,
            'state_id' => $user->state_id,
            'control_number' => $controlNumberService->generateForState($user->state_id, $movementDate),
            'status' => 'ingresado',
            'type' => 'entrada',
        ]);
        
        $movement = InventoryMovement::create($dataToSave);

        return redirect()->route('movements.show_batch', ['batch_id' => $movement->batch_id])
            ->with('success', "Orden de Llenado registrada con el N° de Control: " . $movement->control_number);
    }

    /**
     * Show the form for creating a new BATCH SALIDA resource (Reporte de Ventas).
     */
    public function createBatchSalida()
    {
        $user = Auth::user();
        $products = Product::all();
        $clients = Client::where('state_id', $user->state_id)->get();
        $prices = Price::where('state_id', $user->state_id)
                       ->pluck('price', 'product_id');

        return view('movements.create_batch_salida', compact('products', 'clients', 'prices'));
    }
    
    /**
     * Store a newly created BATCH SALIDA resource in storage.
     */
    public function storeBatchSalida(Request $request, ControlNumberService $controlNumberService)
    {
        $request->validate([
            'movement_date' => 'required|date',
            'quantities' => 'required|array',
        ]);

        $user = Auth::user();
        $batchId = Str::uuid();
        $movementsCreated = 0;
        
        foreach ($request->quantities as $productId => $quantity) {
            if ($quantity > 0) {
                $product = Product::find($productId);
                if (!$product) continue;

                $price = Price::where('product_id', $productId)
                              ->where('state_id', $user->state_id)
                              ->first();
                
                $unitPrice = $price ? $price->price : 0;
                $volumeInLiters = ($product->unit_of_measure === 'kg') ? $quantity * $product->volume_liters : $quantity;
                $totalAmount = $quantity * $unitPrice;

                InventoryMovement::create([
                    'batch_id' => $batchId,
                    'user_id' => $user->id,
                    'state_id' => $user->state_id,
                    'control_number' => $controlNumberService->generateForState($user->state_id, new Carbon($request->movement_date)),
                    'status' => 'ingresado',
                    'type' => 'salida',
                    'movement_date' => $request->movement_date,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'volume_liters' => $volumeInLiters,
                    'unit_price' => $unitPrice,
                    'total_amount' => $totalAmount,
                ]);

                $movementsCreated++;
            }
        }

        if ($movementsCreated == 0) {
            return redirect()->back()->with('warning', 'No se ingresaron cantidades. No se ha registrado ninguna venta.');
        }

        return redirect()->route('movements.show_batch', ['batch_id' => $batchId])
            ->with('success', "Reporte de ventas con $movementsCreated productos guardado exitosamente.");
    }

    /**
     * Display a single resource or a batch of resources.
     */
    public function showBatch($batch_id)
    {
        $movements = InventoryMovement::where('batch_id', $batch_id)
                                      ->with('user', 'state', 'product', 'tank')
                                      ->orderBy('control_number', 'asc')
                                      ->get();

        if ($movements->isEmpty() || (Auth::user()->hasRole('Gerente Regional') && $movements->first()->state_id != Auth::user()->state_id)) {
            abort(404);
        }
        
        $reference = $movements->first();
        
        return view('movements.show_batch', compact('movements', 'reference')); 
    }

    // --- MÉTODOS DEL WORKFLOW ---
    
    public function review(InventoryMovement $movement)
    {
        if (Auth::user()->state_id !== $movement->state_id && !Auth::user()->hasRole('Admin')) {
            abort(403, 'Acción no autorizada.');
        }
    
        if ($movement->type === 'salida' && $movement->batch_id) {
            InventoryMovement::where('batch_id', $movement->batch_id)->update(['status' => 'revisado']);
        } else {
            $movement->update(['status' => 'revisado']);
        }
    
        return redirect()->route('movements.index')->with('success', 'El lote/movimiento ha sido marcado como Revisado.');
    }

    public function approve(InventoryMovement $movement)
    {
        if (Auth::user()->state_id !== $movement->state_id && !Auth::user()->hasRole('Admin')) {
            abort(403, 'Acción no autorizada.');
        }

        if ($movement->type === 'salida' && $movement->batch_id) {
            InventoryMovement::where('batch_id', $movement->batch_id)->update(['status' => 'aprobado']);
        } else {
            $movement->update(['status' => 'aprobado']);
        }

        return redirect()->route('movements.index')->with('success', 'El lote/movimiento ha sido Aprobado.');
    }
    
    // Métodos REST no utilizados
    public function edit(string $id) { abort(404); }
    public function update(Request $request, string $id) { abort(404); }
    public function destroy(string $id) { abort(404); }
}