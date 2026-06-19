<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FipeApiService
{
    private string $baseUrl;
    private int $timeout;
    private int $retries;
    private int $retryDelayMs;
    private string $vehicleType;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('fipe.base_url', 'https://parallelum.com.br/fipe/api/v1'), '/');
        $this->timeout = config('fipe.timeout', 30);
        $this->retries = config('fipe.retries', 3);
        $this->retryDelayMs = config('fipe.retry_delay_ms', 1000);
        $this->vehicleType = config('fipe.vehicle_type', 'carros');
    }

    /**
     * Busca anos disponíveis para um modelo específico na FIPE.
     *
     * @param string $brandFipeCode Código FIPE da marca.
     * @param string $modelFipeCode Código FIPE do modelo.
     * @return array Lista de anos/combustível.
     */
    public function fetchYears(string $brandFipeCode, string $modelFipeCode): array
    {
        $url = "{$this->baseUrl}/{$this->vehicleType}/marcas/{$brandFipeCode}/modelos/{$modelFipeCode}/anos";

        try {
            $data = $this->fetchWithRetry($url);

            return collect($data)->map(function ($item) {
                $nome = $item['nome'] ?? '';
                $codigo = $item['codigo'] ?? '';

                // Parse "2024 Gasolina" → year: 2024, fuel_type: "Gasolina"
                $year = null;
                $fuelType = null;
                if (preg_match('/^(\d{4})\s+(.+)$/', $nome, $matches)) {
                    $year = (int) $matches[1];
                    $fuelType = $matches[2];
                }

                return [
                    'fipe_code' => $codigo,
                    'name' => $nome,
                    'year' => $year,
                    'fuel_type' => $fuelType,
                ];
            })->values()->all();
        } catch (Throwable $e) {
            Log::error("[FIPE] Erro ao buscar anos: " . $e->getMessage());
            throw $e;
        }
    }

    private function fetchWithRetry(string $url): array
    {
        $lastException = null;

        for ($attempt = 1; $attempt <= $this->retries; $attempt++) {
            try {
                $response = Http::timeout($this->timeout)->get($url);

                if ($response->successful()) {
                    return $response->json() ?? [];
                }

                Log::warning("[FIPE] Resposta não bem-sucedida ({$response->status()}) em {$url}. Tentativa {$attempt}.");
            } catch (Throwable $e) {
                $lastException = $e;
                Log::warning("[FIPE] Erro na tentativa {$attempt} para {$url}: " . $e->getMessage());
            }

            if ($attempt < $this->retries) {
                usleep($this->retryDelayMs * 1000);
            }
        }

        throw $lastException ?? new \RuntimeException("Falha ao buscar dados de {$url} após {$this->retries} tentativas.");
    }
}
