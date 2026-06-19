<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Services\FipeImportService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class FipeSeeder extends Seeder
{
    public function run(): void
    {
        $start = microtime(true);
        $service = new FipeImportService();

        $this->command?->info('Iniciando importação FIPE...');
        Log::info('[FIPE Seeder] Iniciando importação completa.');

        $service->fullImport();

        $elapsed = round(microtime(true) - $start, 2);

        $this->command?->info("Importação concluída.");
        $this->command?->info("Marcas importadas: {$service->getBrandsImported()}");
        $this->command?->info("Modelos importados: {$service->getModelsImported()}");
        $this->command?->info("Tempo total: {$elapsed}s");

        Log::info("[FIPE Seeder] Concluído em {$elapsed}s. Marcas: {$service->getBrandsImported()}, Modelos: {$service->getModelsImported()}");
    }
}
