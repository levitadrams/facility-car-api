<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MaintenanceTypeResource;
use App\Models\MaintenanceCategory;
use App\Models\MaintenanceType;
use Illuminate\Http\JsonResponse;

class MaintenanceTypeController extends Controller
{
    /**
     * List all active maintenance types.
     */
    public function index(): JsonResponse
    {
        $types = MaintenanceType::with('maintenanceCategory')
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => MaintenanceTypeResource::collection($types),
        ]);
    }

    /**
     * List types for a specific category.
     */
    public function byCategory(MaintenanceCategory $category): JsonResponse
    {
        $types = $category->maintenanceTypes()
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => MaintenanceTypeResource::collection($types),
        ]);
    }
}
