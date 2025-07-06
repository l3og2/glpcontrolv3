<?php

namespace App\Exports;

use App\Models\InventoryMovement;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InventoryMovementsExport implements FromView
{
    protected $start;
    protected $end;
    protected $type;

    public function __construct($start, $end, $type = null)
    {
        $this->start = $start;
        $this->end = $end;
        $this->type = $type;
    }

    public function view(): View
    {
        $query = InventoryMovement::query();

        if ($this->start && $this->end) {
            $query->whereBetween('created_at', [
                $this->start . ' 00:00:00',
                $this->end . ' 23:59:59'
            ]);
        }
        
        if ($this->type) {
            $query->where('type', $this->type);
        }

        $movements = $query->with('user')->get();

        return view('reports.excel', [
            'movements' => $movements
        ]);
    }
}
