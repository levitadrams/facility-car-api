<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VehicleModelResource;
use App\Models\Brand;
use App\Models\VehicleModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleModelController extends Controller
{
    /**
     * List models for a specific brand with optional search and pagination.
     */
    public function index(Request $request, Brand $brand): JsonResponse
    {
        $query = VehicleModel::where('brand_id', $brand->id)->orderBy('name', 'asc');

        if ($request->filled('search')) {
            $search = strtolower($request->input('search'));
            $query->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
        }

        $perPage = min((int) $request->input('per_page', 15), 100);
        $models = $query->paginate($perPage);

        return response()->json([
            'data' => VehicleModelResource::collection($models),
            'meta' => [
                'current_page' => $models->currentPage(),
                'last_page' => $models->lastPage(),
                'per_page' => $models->perPage(),
                'total' => $models->total(),
            ],
        ]);
    }
}
