<?php

declare(strict_types=1);

return [
    'base_url' => env('FIPE_API_URL', 'https://parallelum.com.br/fipe/api/v1'),
    'timeout' => (int) env('FIPE_TIMEOUT', 30),
    'retries' => (int) env('FIPE_RETRIES', 3),
    'retry_delay_ms' => (int) env('FIPE_RETRY_DELAY_MS', 1000),
    'vehicle_type' => env('FIPE_VEHICLE_TYPE', 'carros'),
];
