<?php

namespace Database\Seeders;

use App\Models\MaintenanceCategory;
use Illuminate\Database\Seeder;

class MaintenanceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Motor', 'description' => 'Serviços relacionados ao motor e seus componentes.', 'icon' => 'construct', 'color' => '#FF5722'],
            ['name' => 'Sistema de Arrefecimento', 'description' => 'Radiador, aditivo, ventoinha e componentes de refrigeração.', 'icon' => 'snow', 'color' => '#03A9F4'],
            ['name' => 'Freios', 'description' => 'Pastilhas, discos, fluido e sistema de frenagem.', 'icon' => 'hand-left', 'color' => '#F44336'],
            ['name' => 'Suspensão', 'description' => 'Amortecedores, molas, buchas e componentes de suspensão.', 'icon' => 'car-sport', 'color' => '#9C27B0'],
            ['name' => 'Direção', 'description' => 'Caixa de direção, terminais, braços axiais e componentes.', 'icon' => 'settings', 'color' => '#3F51B5'],
            ['name' => 'Pneus', 'description' => 'Troca, balanceamento, alinhamento e conservação de pneus.', 'icon' => 'ellipse', 'color' => '#4CAF50'],
            ['name' => 'Transmissão', 'description' => 'Câmbio, embreagem, diferencial e componentes de transmissão.', 'icon' => 'sync', 'color' => '#795548'],
            ['name' => 'Sistema Elétrico', 'description' => 'Alternador, motor de arranque, chicote e componentes elétricos.', 'icon' => 'flash', 'color' => '#FFEB3B'],
            ['name' => 'Iluminação', 'description' => 'Faróis, lanternas, lâmpadas e sistema de iluminação.', 'icon' => 'bulb', 'color' => '#FFC107'],
            ['name' => 'Ar Condicionado', 'description' => 'Climatização, filtros de cabine, compressor e gás refrigerante.', 'icon' => 'thermometer', 'color' => '#00BCD4'],
            ['name' => 'Sistema de Combustível', 'description' => 'Bomba de combustível, bicos injetores, filtros e linha de combustível.', 'icon' => 'water', 'color' => '#8BC34A'],
            ['name' => 'Escapamento', 'description' => 'Catalisador, silenciosos, sondas lambda e tubulação de escape.', 'icon' => 'cloud', 'color' => '#607D8B'],
            ['name' => 'Lataria e Pintura', 'description' => 'Reparos estruturais, polimento, pintura e estética externa.', 'icon' => 'color-fill', 'color' => '#E91E63'],
            ['name' => 'Vidros', 'description' => 'Para-brisa, vidros laterais, traseiro e máquinas de vidro.', 'icon' => 'square', 'color' => '#2196F3'],
            ['name' => 'Limpadores', 'description' => 'Palhetas, motores do limpador e sistema de limpeza de vidros.', 'icon' => 'rainy', 'color' => '#009688'],
            ['name' => 'Acessórios', 'description' => 'Multimídia, alarmes, rastreadores, sensores e acessórios em geral.', 'icon' => 'cube', 'color' => '#673AB7'],
            ['name' => 'Revisões', 'description' => 'Revisões programadas e preventivas por quilometragem.', 'icon' => 'clipboard', 'color' => '#3F51B5'],
            ['name' => 'Documentação', 'description' => 'Licenciamento, IPVA, seguros, vistorias e documentação veicular.', 'icon' => 'document-text', 'color' => '#FF9800'],
            ['name' => 'Serviços Gerais', 'description' => 'Diagnósticos, guinchos, lavagens, higienização e serviços diversos.', 'icon' => 'briefcase', 'color' => '#9E9E9E'],
            ['name' => 'Veículos Elétricos', 'description' => 'Manutenção específica para veículos 100% elétricos.', 'icon' => 'battery-charging', 'color' => '#4CAF50'],
            ['name' => 'Veículos Híbridos', 'description' => 'Manutenção específica para veículos híbridos.', 'icon' => 'git-branch', 'color' => '#009688'],
        ];

        foreach ($categories as $category) {
            MaintenanceCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
