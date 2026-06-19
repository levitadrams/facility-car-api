<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MaintenanceCategoryResource;
use App\Models\MaintenanceCategory;
use Illuminate\Http\JsonResponse;

class MaintenanceCategoryController extends Controller
{
    /**
     * List all active maintenance categories with their types.
     */
    public function index(): JsonResponse
    {
        $categories = MaintenanceCategory::with(['maintenanceTypes' => fn ($q) => $q->where('active', true)])
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => MaintenanceCategoryResource::collection($categories),
        ]);
    }
}
