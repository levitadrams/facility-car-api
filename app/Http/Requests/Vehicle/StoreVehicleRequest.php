<?php

namespace App\Http\Requests\Vehicle;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $currentYear = (int) date('Y');

        return [
            'nickname' => ['nullable', 'string', 'max:100'],
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'vehicle_model_id' => ['required', 'integer', 'exists:vehicle_models,id'],
            'year' => ['required', 'integer', 'min:1980', 'max:' . ($currentYear + 1)],
            'plate' => [
                'required',
                'string',
                'size:7',
                'regex:/^[A-Z]{3}[0-9][A-Z][0-9]{2}$|^[A-Z]{3}[0-9]{4}$/',
                Rule::unique('vehicles', 'plate')->where('user_id', $this->user()->id),
            ],
            'color' => ['nullable', 'string', 'max:50'],
            'fuel_type' => ['nullable', 'string', 'max:50'],
            'current_mileage' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'brand_id.required' => 'A marca é obrigatória.',
            'brand_id.exists' => 'Marca não encontrada.',
            'vehicle_model_id.required' => 'O modelo é obrigatório.',
            'vehicle_model_id.exists' => 'Modelo não encontrado.',
            'year.required' => 'O ano é obrigatório.',
            'year.min' => 'O ano deve ser no mínimo 1980.',
            'year.max' => 'O ano não pode ser maior que ' . ((int) date('Y') + 1) . '.',
            'plate.required' => 'A placa é obrigatória.',
            'plate.size' => 'A placa deve ter exatamente 7 caracteres.',
            'plate.regex' => 'Formato de placa inválido. Use ABC1D23 ou ABC1234.',
            'plate.unique' => 'Você já possui um veículo com esta placa.',
            'current_mileage.required' => 'A quilometragem é obrigatória.',
            'current_mileage.min' => 'A quilometragem não pode ser negativa.',
        ];
    }
}
