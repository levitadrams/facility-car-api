<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Serviço de Roteamento OSRM (Open Source Routing Machine)
 * 
 * Responsável por:
 * - Comunicação com API OSRM
 * - Cálculo de distância e tempo
 * - Aplicação de fatores de correção
 * - Logs de debug
 */
class OsrmService
{
    /**
     * Calcula rota entre dois pontos
     * 
     * @param float $originLat Latitude origem
     * @param float $originLon Longitude origem
     * @param float $destLat Latitude destino
     * @param float $destLon Longitude destino
     * @return array
     */
    public function calculateRoute(
        float $originLat,
        float $originLon,
        float $destLat,
        float $destLon
    ): array {
        $baseUrl = config('routesgo.osrm.base_url');
        $profile = config('routesgo.osrm.profile');
        $timeout = config('routesgo.osrm.timeout');

        // OSRM exige: longitude,latitude (não latitude,longitude)
        $url = "{$baseUrl}/route/v1/{$profile}/{$originLon},{$originLat};{$destLon},{$destLat}";
        $url .= "?overview=false&geometries=geojson";

        // Log de debug (se habilitado)
        if (config('routesgo.enable_debug_logs')) {
            Log::info('OSRM Request', [
                'url' => $url,
                'origin' => ['lat' => $originLat, 'lon' => $originLon],
                'destination' => ['lat' => $destLat, 'lon' => $destLon],
            ]);
        }

        try {
            $response = Http::timeout($timeout)->get($url);

            if (!$response->successful()) {
                Log::error('OSRM API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \Exception("OSRM API error: {$response->status()}");
            }

            $data = $response->json();

            if ($data['code'] !== 'Ok' || empty($data['routes'])) {
                Log::warning('OSRM No Route Found', ['response' => $data]);
                throw new \Exception('Nenhuma rota encontrada');
            }

            $route = $data['routes'][0];
            $rawDistance = $route['distance']; // metros
            $rawDuration = $route['duration']; // segundos

            // Aplicar fator de correção de tráfego
            $trafficFactor = config('routesgo.default_traffic_factor');
            $estimatedDuration = $rawDuration * $trafficFactor;

            $result = [
                'distance' => $rawDistance,
                'duration_calculated' => $rawDuration, // Tempo teórico do OSRM
                'duration_estimated' => $estimatedDuration, // Tempo estimado com fator
                'traffic_factor' => $trafficFactor,
            ];

            // Incluir dados brutos se configurado
            if (config('routesgo.response.include_raw_osrm')) {
                $result['raw_osrm'] = $route;
            }

            // Log de debug
            if (config('routesgo.enable_debug_logs')) {
                Log::info('OSRM Response', [
                    'distance_km' => round($rawDistance / 1000, 2),
                    'duration_calculated_min' => round($rawDuration / 60, 1),
                    'duration_estimated_min' => round($estimatedDuration / 60, 1),
                    'traffic_factor' => $trafficFactor,
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('OSRM Service Error', [
                'message' => $e->getMessage(),
                'origin' => ['lat' => $originLat, 'lon' => $originLon],
                'destination' => ['lat' => $destLat, 'lon' => $destLon],
            ]);

            throw $e;
        }
    }

    /**
     * Calcula múltiplas rotas em paralelo (uso futuro)
     * 
     * @param array $routes Array de rotas [['origin' => [...], 'destination' => [...]]]
     * @return array
     */
    public function calculateMultipleRoutes(array $routes): array
    {
        $results = [];

        foreach ($routes as $index => $route) {
            try {
                $results[$index] = $this->calculateRoute(
                    $route['origin']['lat'],
                    $route['origin']['lon'],
                    $route['destination']['lat'],
                    $route['destination']['lon']
                );
            } catch (\Exception $e) {
                $results[$index] = [
                    'error' => true,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Formata distância para exibição
     * 
     * @param float $meters Distância em metros
     * @return string
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
     * 
     * @param float $seconds Duração em segundos
     * @return string
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
}
