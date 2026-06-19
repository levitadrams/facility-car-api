<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\FipeImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class ImportVehiclesCommand extends Command
{
    protected $signature = 'vehicles:import {--refresh : Atualiza registros existentes}';

    protected $description = 'Importa marcas e modelos de veículos da tabela FIPE.';

    public function handle(): int
    {
        $start = microtime(true);
        $refresh = $this->option('refresh');
        $service = new FipeImportService();

        $this->info('Iniciando importação...');
        $this->info('Buscando marcas...');

        try {
            $service->fullImport($refresh);
        } catch (Throwable $e) {
            $this->error('Erro durante a importação: ' . $e->getMessage());
            Log::error('[ImportVehiclesCommand] Falha: ' . $e->getMessage());
            return self::FAILURE;
        }

        $elapsed = $this->formatElapsed(microtime(true) - $start);

        $this->newLine();
        $this->info('Concluído.');
        $this->info("Marcas: {$service->getBrandsImported()}");
        $this->info("Modelos: {$service->getModelsImported()}");
        $this->info("Tempo: {$elapsed}");

        return self::SUCCESS;
    }

    private function formatElapsed(float $seconds): string
    {
        if ($seconds < 60) {
            return round($seconds, 1) . 's';
        }

        $minutes = floor($seconds / 60);
        $remaining = round($seconds % 60);

        return "{$minutes}m {$remaining}s";
    }
}
