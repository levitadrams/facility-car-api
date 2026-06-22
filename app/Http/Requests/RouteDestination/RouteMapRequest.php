<?php

declare(strict_types=1);

namespace App\Http\Requests\RouteDestination;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para cálculo de rota detalhada (mapa)
 */
class RouteMapRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    /**
     * Mensagens customizadas de validação
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'latitude.required'  => 'A latitude da origem é obrigatória.',
            'latitude.numeric'   => 'A latitude deve ser um número válido.',
            'latitude.between'   => 'A latitude deve estar entre -90 e 90.',
            'longitude.required' => 'A longitude da origem é obrigatória.',
            'longitude.numeric'  => 'A longitude deve ser um número válido.',
            'longitude.between'  => 'A longitude deve estar entre -180 e 180.',
        ];
    }
}
