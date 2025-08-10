<?php

namespace App\Services;

use App\Models\InventoryMovement;
use App\Models\DailyClosing;
use App\Models\Product;
use Carbon\Carbon;

class InventoryService
{
    /**
     * Recopila y calcula todos los datos necesarios para la vista de Cierre Diario.
     *
     * @param int $stateId
     * @param Carbon $date
     * @return array
     */
    public function getDailyClosingData(int $stateId, Carbon $date): array
    {
        // 1. Buscar el inventario final del último cierre registrado para este estado
        $lastClosing = DailyClosing::where('state_id', $stateId)
                                   ->where('closing_date', '<', $date->copy()->startOfDay())
                                   ->orderBy('closing_date', 'desc')
                                   ->first();
        
        $initialInventory = $lastClosing ? $lastClosing->theorical_inventory : 0;

        // 2. Obtener todos los movimientos de salida APROBADOS del día
        $dailyExits = InventoryMovement::where('state_id', $stateId)
            ->where('type', 'salida')
            ->where('status', 'aprobado')
            ->whereDate('movement_date', $date)
            ->with('product') // Precargamos la relación con el producto para eficiencia
            ->get();

        // 3. Agrupamos y sumamos las ventas por producto
        $salesSummary = $dailyExits->groupBy('product_id')->map(function ($group) {
            return [
                'product_name' => $group->first()->product->name,
                'quantity' => $group->sum('quantity'),
                'total_volume' => $group->sum('volume_liters'),
                'total_sales' => $group->sum('total_amount'),
            ];
        });

        // 4. Ahora agrupamos el resumen de ventas por categoría (Residencial, Comercial, Convenios)
        $salesByCategory = $salesSummary->groupBy(function ($item) {
            if (str_contains($item['product_name'], '-Res')) return 'Residencial';
            if (str_contains($item['product_name'], '-Com')) return 'Comercial';
            return 'Convenios';
        });

        // 5. Calculamos las entradas totales aprobadas del día
        $totalEntries = InventoryMovement::where('state_id', $stateId)
            ->where('type', 'entrada')
            ->where('status', 'aprobado')
            ->whereDate('movement_date', $date)
            ->sum('volume_liters');
        
        // 6. Calculamos los totales finales
        $totalExits = $dailyExits->sum('volume_liters');
        $theoricalInventory = $initialInventory + $totalEntries - $totalExits;

        // ==================== INICIO DE LA CORRECCIÓN ====================
        // 7. Calculamos el total de unidades de CILINDROS vendidos, excluyendo Granel y Carburación
        $totalCylinderUnits = $dailyExits->filter(function ($movement) {
            return $movement->product->type === 'cilindro';
        })->sum('quantity');
        // ===================== FIN DE LA CORRECCIÓN ======================

        // 8. Definimos un orden personalizado para los productos
        $customOrder = [
            'Cilindros 10KG' => 1,
            'Cilindros 18KG' => 2,
            'Cilindros 27KG' => 3,
            'Cilindros 43KG' => 4,
            'Granel Litro' => 5,
            'Carburación Litro' => 6,
        ];

        // Obtenemos todos los productos y los ordenamos según nuestro criterio personalizado
        $allProducts = Product::all()->sortBy(function ($product) use ($customOrder) {
            $baseName = trim(str_replace(['-Res', '-Com', '-Conv'], '', $product->name));
            return $customOrder[$baseName] ?? 99; // Si un producto no está en la lista, va al final
        });
        
        // 9. Devolvemos la estructura de datos completa que la vista necesita
        return [
            'initial_inventory' => $initialInventory,
            'total_entries' => $totalEntries,
            'total_exits' => $totalExits,
            'theorical_inventory' => $theoricalInventory,
            'sales_summary' => $salesByCategory,
            'all_products' => $allProducts->groupBy(function ($item) {
                 if (str_contains($item->name, '-Res')) return 'Residencial';
                 if (str_contains($item->name, '-Com')) return 'Comercial';
                 return 'Convenios';
            }),
            'total_cylinder_units' => $totalCylinderUnits, // <-- DEVOLVEMOS EL NUEVO DATO
        ];
    }
}