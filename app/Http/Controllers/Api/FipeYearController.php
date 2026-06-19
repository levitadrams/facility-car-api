<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\VehicleModel;
use App\Services\FipeApiService;
use Illuminate\Http\JsonResponse;
use Throwable;

class FipeYearController extends Controller
{
    public function __construct(private readonly FipeApiService $fipeApi) {}

    public function index(Brand $brand, VehicleModel $vehicleModel): JsonResponse
    {
        if (empty($brand->fipe_code) || empty($vehicleModel->fipe_code)) {
            return response()->json([
                'data' => [],
                'message' => 'Código FIPE não disponível para esta marca ou modelo.',
            ]);
        }

        try {
            $years = $this->fipeApi->fetchYears($brand->fipe_code, $vehicleModel->fipe_code);

            return response()->json(['data' => $years]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erro ao buscar anos da FIPE.',
                'error' => $e->getMessage(),
            ], 502);
        }
    }
}
