<?php

namespace App\Http\Requests\Maintenance;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'maintenance_type_id' => ['required', 'integer', 'exists:maintenance_types,id'],
            'description' => ['nullable', 'string'],
            'performed_at' => ['required', 'date'],
            'current_mileage' => ['required', 'integer', 'min:0'],
            'cost' => ['required', 'numeric', 'min:0'],
            'workshop_name' => ['nullable', 'string', 'max:200'],
            'invoice_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'next_maintenance_mileage' => ['nullable', 'integer', 'min:0'],
            'next_maintenance_date' => ['nullable', 'date', 'after_or_equal:performed_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_id.required' => 'O veículo é obrigatório.',
            'vehicle_id.exists' => 'Veículo não encontrado.',
            'maintenance_type_id.required' => 'O tipo de manutenção é obrigatório.',
            'maintenance_type_id.exists' => 'O tipo de manutenção selecionado é inválido.',
            'performed_at.required' => 'A data da manutenção é obrigatória.',
            'performed_at.date' => 'A data da manutenção deve ser uma data válida.',
            'current_mileage.required' => 'A quilometragem atual é obrigatória.',
            'current_mileage.min' => 'A quilometragem não pode ser negativa.',
            'cost.required' => 'O valor é obrigatório.',
            'cost.numeric' => 'O valor deve ser numérico.',
            'cost.min' => 'O valor não pode ser negativo.',
            'workshop_name.max' => 'O nome da oficina não pode ter mais de 200 caracteres.',
            'invoice_number.max' => 'O número da nota fiscal não pode ter mais de 100 caracteres.',
            'next_maintenance_mileage.min' => 'A quilometragem da próxima manutenção não pode ser negativa.',
            'next_maintenance_date.after_or_equal' => 'A próxima data de manutenção deve ser igual ou posterior à data da manutenção atual.',
        ];
    }
}
