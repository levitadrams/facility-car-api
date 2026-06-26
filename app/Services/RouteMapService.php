<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Serviço de Mapa de Rotas (RotasGo)
 *
 * Responsável por:
 * - Consultar OSRM com overview completo e geometria GeoJSON
 * - Transformar resposta OSRM em formato amigável ao frontend
 * - Extrair steps para futura tela de "Passo a Passo da Rota"
 * - Tratamento de erros (timeout, indisponibilidade, sem rota)
 */
class RouteMapService
{
    /**
     * Calcula rota detalhada com geometria completa (polyline)
     *
     * @param float $originLat  Latitude da origem
     * @param float $originLon  Longitude da origem
     * @param float $destLat    Latitude do destino
     * @param float $destLon    Longitude do destino
     * @return array
     * @throws \Exception
     */
    public function calculateDetailedRoute(
        float $originLat,
        float $originLon,
        float $destLat,
        float $destLon
    ): array {
        $baseUrl = config('routesgo.osrm.base_url');
        $profile = config('routesgo.osrm.profile');
        $timeout = config('routesgo.osrm.timeout');

        // OSRM exige: longitude,latitude (não latitude,longitude)
        $coordinates = "{$originLon},{$originLat};{$destLon},{$destLat}";
        $url = "{$baseUrl}/route/v1/{$profile}/{$coordinates}";
        $url .= "?overview=full&geometries=geojson&steps=true";

        if (config('routesgo.enable_debug_logs')) {
            Log::info('RouteMapService OSRM Request', [
                'url'       => $url,
                'origin'    => ['lat' => $originLat, 'lon' => $originLon],
                'destination' => ['lat' => $destLat, 'lon' => $destLon],
            ]);
        }

        try {
            $response = Http::timeout($timeout)->get($url);

            if (! $response->successful()) {
                Log::error('RouteMapService OSRM API Error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                throw new \Exception('OSRM indisponível no momento');
            }

            $data = $response->json();

            if (empty($data) || ($data['code'] ?? '') !== 'Ok' || empty($data['routes'])) {
                Log::warning('RouteMapService OSRM No Route Found', ['response' => $data]);
                throw new \Exception('Nenhuma rota encontrada entre os pontos informados');
            }

            $route = $data['routes'][0];
            $rawDistance = (float) ($route['distance'] ?? 0);   // metros
            $rawDuration = (float) ($route['duration'] ?? 0);   // segundos

            // Fator de correção de tráfego dinâmico (horário + distância)
            $trafficFactor = $this->calculateDynamicTrafficFactor($rawDistance);
            $estimatedDuration = $rawDuration * $trafficFactor;

            // Extrair geometria (GeoJSON → array de coordenadas lat/lng)
            $geometry = $this->extractGeometry($route);

            // Extrair steps para futura tela de instruções passo a passo
            $steps = $this->extractSteps($route);

            // Instruções textuais resumidas
            $instructions = $this->extractInstructions($route);

            $result = [
                'distance'            => $rawDistance,
                'duration_calculated' => $rawDuration,
                'duration_estimated'  => $estimatedDuration,
                'traffic_factor'      => $trafficFactor,
                'geometry'            => $geometry,
                'steps'               => $steps,
                'instructions'        => $instructions,
            ];

            if (config('routesgo.enable_debug_logs')) {
                Log::info('RouteMapService OSRM Response', [
                    'distance_km'             => round($rawDistance / 1000, 2),
                    'duration_calculated_min' => round($rawDuration / 60, 1),
                    'duration_estimated_min'  => round($estimatedDuration / 60, 1),
                    'geometry_points'         => count($geometry),
                    'steps_count'             => count($steps),
                ]);
            }

            return $result;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('RouteMapService OSRM Timeout', [
                'message' => $e->getMessage(),
            ]);
            throw new \Exception('Tempo de resposta do OSRM excedido. Tente novamente.');
        } catch (\Exception $e) {
            Log::error('RouteMapService Error', [
                'message' => $e->getMessage(),
                'origin'    => ['lat' => $originLat, 'lon' => $originLon],
                'destination' => ['lat' => $destLat, 'lon' => $destLon],
            ]);
            throw $e;
        }
    }

    /**
     * Extrai geometria GeoJSON do OSRM em array de coordenadas {latitude, longitude}
     */
    private function extractGeometry(array $route): array
    {
        if (! isset($route['geometry']['coordinates']) || ! is_array($route['geometry']['coordinates'])) {
            return [];
        }

        return array_map(
            fn (array $coord) => [
                'latitude'  => (float) $coord[1],
                'longitude' => (float) $coord[0],
            ],
            $route['geometry']['coordinates']
        );
    }

    /**
     * Extrai steps detalhados de cada leg da rota para futura tela de instruções
     */
    private function extractSteps(array $route): array
    {
        $steps = [];

        if (empty($route['legs'])) {
            return $steps;
        }

        foreach ($route['legs'] as $leg) {
            if (empty($leg['steps'])) {
                continue;
            }

            foreach ($leg['steps'] as $step) {
                $stepGeometry = [];
                if (isset($step['geometry']['coordinates']) && is_array($step['geometry']['coordinates'])) {
                    $stepGeometry = array_map(
                        fn (array $coord) => [
                            'latitude'  => (float) $coord[1],
                            'longitude' => (float) $coord[0],
                        ],
                        $step['geometry']['coordinates']
                    );
                }

                $steps[] = [
                    'instruction' => $step['name'] ?? '',
                    'distance'    => (float) ($step['distance'] ?? 0),
                    'duration'    => (float) ($step['duration'] ?? 0),
                    'type'        => $step['maneuver']['type'] ?? '',
                    'modifier'    => $step['maneuver']['modifier'] ?? '',
                    'geometry'    => $stepGeometry,
                ];
            }
        }

        return $steps;
    }

    /**
     * Extrai instruções textuais resumidas (nome de cada step)
     */
    private function extractInstructions(array $route): array
    {
        $instructions = [];

        if (empty($route['legs'])) {
            return $instructions;
        }

        foreach ($route['legs'] as $leg) {
            if (empty($leg['steps'])) {
                continue;
            }

            foreach ($leg['steps'] as $step) {
                $instructions[] = $step['name'] ?? '';
            }
        }

        return array_filter($instructions);
    }

    /**
     * Formata distância para exibição
     */
    public static function formatDistance(float $meters): string
    {
        if ($meters < 1000) {
            return round($meters) . ' m';
        }

        return number_format($meters / 1000, 1, ',', '.') . ' km';
    }

    /**
     * Formata duração para exibição
     */
    public static function formatDuration(float $seconds): string
    {
        if ($seconds < 60) {
            return round($seconds) . ' s';
        }

        $minutes = round($seconds / 60);

        if ($minutes < 60) {
            return $minutes . ' min';
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        return "{$hours}h {$remainingMinutes}min";
    }

    /**
     * Calcula fator de tráfego dinâmico baseado em horário e distância
     * 
     * @param float $distance Distância da rota em metros
     * @return float Fator de tráfego calculado
     */
    private function calculateDynamicTrafficFactor(float $distance): float
    {
        // 1. Obtém fator base por horário
        $timeFactor = $this->getTimeBasedFactor();

        // 2. Obtém multiplicador por distância
        $distanceMultiplier = $this->getDistanceMultiplier($distance);

        // 3. Calcula fator final
        $finalFactor = $timeFactor * $distanceMultiplier;

        // 4. Garante limites razoáveis (mínimo 1.1, máximo 2.5)
        return max(1.1, min(2.5, $finalFactor));
    }

    /**
     * Obtém fator de tráfego baseado no horário atual
     * 
     * @return float
     */
    private function getTimeBasedFactor(): float
    {
        $currentHour = (int) date('H');
        $timeFactors = config('routesgo.time_based_factors');

        foreach ($timeFactors as $period => $config) {
            if ($currentHour >= $config['start'] && $currentHour < $config['end']) {
                return $config['factor'];
            }
        }

        // Fallback para fator padrão
        return config('routesgo.default_traffic_factor');
    }

    /**
     * Obtém multiplicador baseado na distância da rota
     * 
     * @param float $distance Distância em metros
     * @return float
     */
    private function getDistanceMultiplier(float $distance): float
    {
        $adjustments = config('routesgo.distance_adjustments');

        if ($distance < $adjustments['short']['threshold']) {
            return $adjustments['short']['multiplier'];
        }

        if ($distance < $adjustments['medium']['threshold']) {
            return $adjustments['medium']['multiplier'];
        }

        return $adjustments['long']['multiplier'];
    }
}
