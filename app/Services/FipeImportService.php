<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Brand;
use App\Models\VehicleModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FipeImportService
{
    private string $baseUrl;
    private int $timeout;
    private int $retries;
    private int $retryDelayMs;
    private string $vehicleType;
    private int $brandsImported = 0;
    private int $modelsImported = 0;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('fipe.base_url', 'https://parallelum.com.br/fipe/api/v1'), '/');
        $this->timeout = config('fipe.timeout', 30);
        $this->retries = config('fipe.retries', 3);
        $this->retryDelayMs = config('fipe.retry_delay_ms', 1000);
        $this->vehicleType = config('fipe.vehicle_type', 'carros');
    }

    /**
     * Importa todas as marcas e modelos da API FIPE.
     *
     * @param bool $refresh Atualiza registros existentes.
     */
    public function fullImport(bool $refresh = false): void
    {
        $this->brandsImported = 0;
        $this->modelsImported = 0;

        Log::info('[FIPE] Iniciando importação...');
        $this->importBrands();
        $this->importModels($refresh);
        Log::info("[FIPE] Importação concluída. Marcas: {$this->brandsImported}, Modelos: {$this->modelsImported}");
    }

    /**
     * Busca e salva marcas.
     */
    public function importBrands(): void
    {
        $url = "{$this->baseUrl}/{$this->vehicleType}/marcas";
        $brands = $this->fetchWithRetry($url);

        if (empty($brands)) {
            Log::warning('[FIPE] Nenhuma marca retornada pela API.');
            return;
        }

        $upsertData = [];
        foreach ($brands as $brand) {
            $upsertData[] = [
                'name' => $brand['nome'] ?? $brand['name'] ?? 'Desconhecida',
                'fipe_code' => $brand['codigo'] ?? $brand['code'] ?? null,
                'updated_at' => now(),
                'created_at' => now(),
            ];
        }

        Brand::upsert($upsertData, ['fipe_code'], ['name', 'updated_at']);

        $this->brandsImported = count($brands);
        Log::info("[FIPE] Marcas importadas: {$this->brandsImported}");
    }

    /**
     * Busca e salva modelos de todas as marcas.
     *
     * @param bool $refresh Atualiza registros existentes.
     */
    public function importModels(bool $refresh = false): void
    {
        $brands = Brand::all(['id', 'fipe_code', 'name']);

        foreach ($brands as $brand) {
            if (empty($brand->fipe_code)) {
                Log::warning("[FIPE] Marca {$brand->name} sem fipe_code, pulando.");
                continue;
            }

            try {
                $this->importModelsForBrand($brand, $refresh);
            } catch (Throwable $e) {
                Log::error("[FIPE] Erro ao importar modelos da marca {$brand->name}: " . $e->getMessage());
            }
        }
    }

    /**
     * Importa modelos de uma marca específica.
     */
    private function importModelsForBrand(Brand $brand, bool $refresh): void
    {
        $url = "{$this->baseUrl}/{$this->vehicleType}/marcas/{$brand->fipe_code}/modelos";
        $data = $this->fetchWithRetry($url);
        $models = $data['modelos'] ?? [];

        if (empty($models)) {
            Log::warning("[FIPE] Nenhum modelo para marca {$brand->name}");
            return;
        }

        Log::info("[FIPE] Importando modelos da marca {$brand->name}: " . count($models));

        $upsertData = [];
        foreach ($models as $model) {
            $upsertData[] = [
                'brand_id' => $brand->id,
                'name' => $model['nome'] ?? $model['name'] ?? 'Desconhecido',
                'fipe_code' => $model['codigo'] ?? $model['code'] ?? null,
                'updated_at' => now(),
                'created_at' => now(),
            ];
        }

        $uniqueBy = ['brand_id', 'fipe_code'];
        $updateColumns = $refresh ? ['name', 'updated_at'] : ['updated_at'];

        VehicleModel::upsert($upsertData, $uniqueBy, $updateColumns);

        $this->modelsImported += count($models);
    }

    /**
     * Executa requisição HTTP com retry e timeout.
     *
     * @param string $url
     * @return array
     * @throws Throwable
     */
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

    public function getBrandsImported(): int
    {
        return $this->brandsImported;
    }

    public function getModelsImported(): int
    {
        return $this->modelsImported;
    }
}
