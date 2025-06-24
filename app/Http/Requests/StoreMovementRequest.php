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
            'type' => 'required|in:entrada,salida',
            'movement_date' => 'required|date',
            'volume_liters' => 'required|numeric|min:0',
            'tank_id' => 'required_if:type,entrada|exists:tanks,id', // El tanque es requerido si es una entrada
            'product_id' => 'required_if:type,salida|exists:products,id', // El producto es requerido si es una salida
            'client_id' => 'nullable|exists:clients,id',
            'notes' => 'nullable|string',
        ];
    }
}
