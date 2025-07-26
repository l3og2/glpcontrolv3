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

class InventoryMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     * Muestra una lista combinada de entradas individuales y salidas agrupadas por lote.
     */
    public function index()
    {
        $query = InventoryMovement::query();
        $user = Auth::user();

        // Filtramos por estado para Gerentes Regionales
        if ($user->hasRole('Gerente Regional')) {
            $query->where('state_id', $user->state_id);
        }
    
        // La consulta principal y simplificada
        $movements = $query->with('user', 'tank', 'product')
            ->select(
                'batch_id',
                'type',
                'user_id',
                'movement_date',
                'status',
                'tank_id',
                DB::raw('SUM(volume_liters) as total_volume'),
                DB::raw('SUM(total_amount) as total_sales'),
                DB::raw('MIN(control_number) as first_control_number'),
                DB::raw('COUNT(*) as item_count') // Contamos cuántos items hay en el lote
            )
            ->groupBy('batch_id', 'type', 'user_id', 'movement_date', 'status', 'tank_id')
            ->orderBy('movement_date', 'desc')
            ->paginate(15);
    
        return view('movements.index', compact('movements'));   
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
        $controlNumber = $controlNumberService->generateForState(Auth::user()->state_id, new Carbon($request->movement_date));
        $validatedData = $request->validated();

        $dataToSave = array_merge($validatedData, [
            'batch_id' => Str::uuid(),
            'user_id' => Auth::id(),
            'state_id' => Auth::user()->state_id,
            'control_number' => $controlNumber,
            'status' => 'ingresado',
            'type' => 'entrada',
        ]);
        
        InventoryMovement::create($dataToSave);

        return redirect()->route('movements.index')
            ->with('success', "Orden de Llenado registrada con el N° de Control: $controlNumber");
    }

    /**
     * Show the form for creating a new BATCH SALIDA resource (Reporte de Ventas).
     */
    public function createBatchSalida()
    {
        $products = Product::all();
        $prices = Price::where('state_id', Auth::user()->state_id)
                       ->pluck('price', 'product_id');

        return view('movements.create_batch_salida', compact('products', 'prices'));
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
        $totalVolume = 0;
        $totalSales = 0;

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
                
                $totalVolume += $volumeInLiters;
                $totalSales += $totalAmount;
                
                $controlNumber = $controlNumberService->generateForState($user->state_id, new Carbon($request->movement_date));

                InventoryMovement::create([
                    'batch_id' => $batchId,
                    'user_id' => $user->id,
                    'state_id' => $user->state_id,
                    'control_number' => $controlNumber,
                    'status' => 'ingresado',
                    'type' => 'salida',
                    'movement_date' => $request->movement_date,
                    'product_id' => $productId,
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

        return redirect()->route('movements.index')
            ->with('success', "Reporte de ventas con $movementsCreated productos guardado exitosamente.");
    }

    // Los métodos restantes permanecen como placeholders para futuras implementaciones.
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}