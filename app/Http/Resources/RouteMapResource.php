<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource para resposta de mapa de rotas (RotasGo)
 *
 * Formata os dados da rota calculada pelo OSRM para consumo do frontend.
 * Inclui geometria completa (polyline), steps detalhados e instruções
 * para futura tela de "Passo a Passo da Rota".
 */
class RouteMapResource extends JsonResource
{
    /**
     * Transforma o recurso em array para resposta JSON
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var array $this->resource */
        $route = $this->resource;

        return [
            'distance_km'       => round(($route['distance'] ?? 0) / 1000, 2),
            'duration_minutes'  => round(($route['duration_estimated'] ?? 0) / 60, 1),
            'distance_raw'      => $route['distance'] ?? 0,
            'duration_raw'      => $route['duration_estimated'] ?? 0,
            'traffic_factor'    => $route['traffic_factor'] ?? 1.0,

            'origin' => [
                'latitude'  => (float) ($route['origin']['latitude'] ?? 0),
                'longitude' => (float) ($route['origin']['longitude'] ?? 0),
                'label'     => 'Minha Localização',
            ],

            'destination' => [
                'latitude'  => (float) ($route['destination']['latitude'] ?? 0),
                'longitude' => (float) ($route['destination']['longitude'] ?? 0),
                'name'      => $route['destination']['name'] ?? '',
            ],

            'geometry' => $route['geometry'] ?? [],

            // Steps detalhados para futura tela de instruções passo a passo
            'steps' => $route['steps'] ?? [],

            // Instruções textuais resumidas
            'instructions' => $route['instructions'] ?? [],
        ];
    }
}
