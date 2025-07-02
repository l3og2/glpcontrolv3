<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovementRequest extends FormRequest
{
    /**
     * Cualquiera que haya pasado el middleware de la ruta está autorizado
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'movement_date'                 => 'required|date',
            'supply_source'                 => 'required|string|max:255',
            'pdvsa_sale_number'             => 'required|string|max:255',
            'chuto_code'                    => 'nullable|string|max:50',
            'chuto_plate'                   => 'required|string|max:20',
            'cisterna_code'                 => 'nullable|string|max:50',
            'cisterna_capacity_gallons'     => 'required|numeric|min:0',
            'cisterna_plate'                => 'required|string|max:20',
            'cisterna_serial'               => 'nullable|string|max:100',
            'driver_name'                   => 'required|string|max:255',
            'driver_ci'                     => 'required|string|max:20',
            'driver_code'                   => 'nullable|string|max:50',
            'tank_id'                       => 'required|integer|exists:tanks,id',
            'arrival_volume_percentage'     => 'required|numeric|between:0,100',
            'arrival_temperature'           => 'required|numeric',
            'arrival_pressure'              => 'required|numeric',
            'arrival_specific_gravity'      => 'required|numeric|min:0',
            'departure_volume_percentage'   => 'required|numeric|between:0,100',
            'departure_temperature'         => 'required|numeric',
            'departure_pressure'            => 'required|numeric',
            'departure_specific_gravity'    => 'required|numeric|min:0',
            'volume_liters'                 => 'required|numeric|min:0',
        ];
    }
}
