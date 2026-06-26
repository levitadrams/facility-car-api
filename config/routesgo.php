<?php

return [

    /*
    |--------------------------------------------------------------------------
    | RotasGo - Fatores de Correção de Tempo
    |--------------------------------------------------------------------------
    |
    | O OSRM calcula tempo baseado em velocidade teórica sem considerar:
    | - Trânsito em tempo real
    | - Semáforos e cruzamentos
    | - Congestionamentos
    | - Condições reais da via
    |
    | Estes fatores aproximam o tempo calculado da realidade.
    |
    */

    /*
    | Fator padrão para cálculo de tempo estimado
    | Multiplica o tempo do OSRM para aproximar da realidade
    | 
    | Calibrado com rotas reais:
    | Exemplo: 47 min (OSRM) × 1.65 = 77 min (próximo de 78 min Google Maps)
    | 
    | Ajuste conforme a região:
    | - 1.65 = adiciona 65% ao tempo (áreas urbanas brasileiras - RECOMENDADO)
    | - 1.50 = adiciona 50% ao tempo (áreas metropolitanas médias)
    | - 1.80 = adiciona 80% ao tempo (grandes metrópoles congestionadas)
    | - 1.20 = adiciona 20% ao tempo (rodovias)
    */
    'default_traffic_factor' => env('ROUTESGO_TRAFFIC_FACTOR', 1.65),

    /*
    | Fatores específicos por tipo de área (uso futuro)
    */
    'traffic_factors' => [
        'urban' => 1.65,        // Área urbana brasileira (calibrado)
        'suburban' => 1.45,     // Área suburbana
        'highway' => 1.15,      // Rodovias
        'metropolitan' => 1.80, // Metrópole congestionada (SP, RJ)
        'rush_hour' => 2.00,    // Horário de pico
    ],

    /*
    |--------------------------------------------------------------------------
    | Fatores Dinâmicos por Horário
    |--------------------------------------------------------------------------
    |
    | Ajusta o fator de tráfego baseado no horário do dia.
    | Considera padrões de tráfego urbano brasileiro.
    |
    */
    'time_based_factors' => [
        'dawn' => [              // Madrugada (00h-06h)
            'start' => 0,
            'end' => 6,
            'factor' => 1.15,    // Vias livres
        ],
        'morning_rush' => [      // Pico da manhã (06h-09h)
            'start' => 6,
            'end' => 9,
            'factor' => 2.00,    // Trânsito intenso
        ],
        'daytime' => [           // Dia (09h-17h)
            'start' => 9,
            'end' => 17,
            'factor' => 1.50,    // Moderado
        ],
        'evening_rush' => [      // Pico da tarde (17h-20h)
            'start' => 17,
            'end' => 20,
            'factor' => 2.10,    // Trânsito muito intenso
        ],
        'night' => [             // Noite (20h-00h)
            'start' => 20,
            'end' => 24,
            'factor' => 1.30,    // Moderado/leve
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Ajuste por Distância
    |--------------------------------------------------------------------------
    |
    | Rotas curtas têm mais impacto de semáforos e trânsito urbano.
    | Rotas longas têm mais trechos de rodovia com fluxo livre.
    |
    */
    'distance_adjustments' => [
        'short' => [             // < 5km
            'threshold' => 5000, // metros
            'multiplier' => 1.15, // Mais semáforos
        ],
        'medium' => [            // 5-20km
            'threshold' => 20000,
            'multiplier' => 1.00, // Balanceado
        ],
        'long' => [              // > 20km
            'threshold' => PHP_INT_MAX,
            'multiplier' => 0.85, // Mais rodovias
        ],
    ],

    /*
    | Configuração do servidor OSRM
    */
    'osrm' => [
        'base_url' => env('OSRM_BASE_URL', 'https://router.project-osrm.org'),
        'profile' => env('OSRM_PROFILE', 'driving'),
        'timeout' => env('OSRM_TIMEOUT', 10), // segundos
    ],

    /*
    | Habilitar logs de debug para análise
    */
    'enable_debug_logs' => env('ROUTESGO_DEBUG', false),

    /*
    | Formato de resposta
    */
    'response' => [
        'include_raw_osrm' => env('ROUTESGO_INCLUDE_RAW', false), // Incluir dados brutos do OSRM
        'include_calculated_time' => true,  // Tempo calculado (sem fator)
        'include_estimated_time' => true,   // Tempo estimado (com fator)
    ],

];
