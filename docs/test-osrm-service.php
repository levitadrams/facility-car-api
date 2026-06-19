<?php

/**
 * Teste do OsrmService via Artisan Tinker
 * 
 * Como usar:
 * 1. Abra o terminal no projeto Laravel
 * 2. Execute: php artisan tinker
 * 3. Cole e execute este código
 * 
 * Ou execute diretamente:
 * php artisan tinker < docs/test-osrm-service.php
 */

use App\Services\OsrmService;

echo "\n";
echo "================================================\n";
echo "🧪 Teste OsrmService - RotasGo\n";
echo "================================================\n\n";

// Criar instância do serviço
$osrm = new OsrmService();

// Exemplo: Rio de Janeiro (Centro) → Copacabana
$originLat = -22.9083;
$originLon = -43.1964;
$destLat = -22.9711;
$destLon = -43.1822;

echo "📍 Origem: Lat $originLat, Lon $originLon\n";
echo "📍 Destino: Lat $destLat, Lon $destLon\n\n";

echo "🚀 Calculando rota...\n\n";

try {
    $result = $osrm->calculateRoute(
        $originLat,
        $originLon,
        $destLat,
        $destLon
    );

    echo "✅ Sucesso!\n\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📊 Resultados:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    $distanceKm = round($result['distance'] / 1000, 2);
    $durationCalcMin = round($result['duration_calculated'] / 60, 1);
    $durationEstMin = round($result['duration_estimated'] / 60, 1);
    $factor = $result['traffic_factor'];

    echo "🧭 Distância:\n";
    echo "   └─ {$result['distance']} metros\n";
    echo "   └─ $distanceKm km\n\n";

    echo "⏱️  Tempo Calculado (OSRM teórico):\n";
    echo "   └─ {$result['duration_calculated']} segundos\n";
    echo "   └─ $durationCalcMin minutos\n\n";

    echo "⏰ Tempo Estimado (com fator de tráfego):\n";
    echo "   └─ {$result['duration_estimated']} segundos\n";
    echo "   └─ $durationEstMin minutos\n\n";

    echo "📈 Fator de Correção:\n";
    echo "   └─ $factor (+" . (($factor - 1) * 100) . "%)\n\n";

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "💡 Comparação:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "OSRM (teórico):     $durationCalcMin min\n";
    echo "Estimado (real):    $durationEstMin min\n";
    echo "Diferença:          +" . round($durationEstMin - $durationCalcMin, 1) . " min\n\n";

    echo "✨ Formatado:\n";
    echo "   Distância: " . OsrmService::formatDistance($result['distance']) . "\n";
    echo "   Calculado: " . OsrmService::formatDuration($result['duration_calculated']) . "\n";
    echo "   Estimado:  " . OsrmService::formatDuration($result['duration_estimated']) . "\n\n";

} catch (Exception $e) {
    echo "❌ Erro: {$e->getMessage()}\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📋 Configuração Atual:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
echo "Fator de Tráfego: " . config('routesgo.default_traffic_factor') . "\n";
echo "Debug Logs: " . (config('routesgo.enable_debug_logs') ? 'Habilitado' : 'Desabilitado') . "\n";
echo "OSRM URL: " . config('routesgo.osrm.base_url') . "\n";
echo "Perfil: " . config('routesgo.osrm.profile') . "\n\n";

echo "🏁 Teste concluído!\n\n";
