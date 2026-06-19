<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vehicle\StoreVehicleRequest;
use App\Http\Requests\Vehicle\UpdateVehicleRequest;
use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * List all vehicles for the authenticated user with pagination, search and sorting.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Vehicle::with(['brand', 'vehicleModel'])
            ->where('user_id', $request->user()->id);

        // Search (via joins on related tables)
        if ($request->filled('search')) {
            $search = strtolower($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(plate) LIKE ?', ["%{$search}%"])
                  ->orWhereHas('brand', fn ($b) => $b->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]))
                  ->orWhereHas('vehicleModel', fn ($m) => $m->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]));
            });
        }

        // Sorting
        $sort = $request->input('sort', 'recent');
        match ($sort) {
            'oldest' => $query->orderBy('created_at', 'asc'),
            'mileage' => $query->orderBy('current_mileage', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $vehicles = $query->paginate(15);

        return response()->json([
            'data' => VehicleResource::collection($vehicles),
            'meta' => [
                'current_page' => $vehicles->currentPage(),
                'last_page' => $vehicles->lastPage(),
                'per_page' => $vehicles->perPage(),
                'total' => $vehicles->total(),
            ],
        ]);
    }

    /**
     * Store a new vehicle.
     */
    public function store(StoreVehicleRequest $request): JsonResponse
    {
        $vehicle = Vehicle::create([
            'user_id' => $request->user()->id,
            'nickname' => $request->nickname,
            'brand_id' => $request->brand_id,
            'vehicle_model_id' => $request->vehicle_model_id,
            'year' => $request->year,
            'plate' => $request->plate,
            'color' => $request->color,
            'fuel_type' => $request->fuel_type,
            'current_mileage' => $request->current_mileage,
        ]);

        $vehicle->load(['brand', 'vehicleModel']);

        return response()->json([
            'message' => 'Veículo cadastrado com sucesso.',
            'data' => new VehicleResource($vehicle),
        ], 201);
    }

    /**
     * Show a specific vehicle.
     */
    public function show(Request $request, Vehicle $vehicle): JsonResponse
    {
        if ($vehicle->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $vehicle->load(['brand', 'vehicleModel']);

        return response()->json([
            'data' => new VehicleResource($vehicle),
        ]);
    }

    /**
     * Update a vehicle.
     */
    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): JsonResponse
    {
        if ($vehicle->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $vehicle->update([
            'nickname' => $request->nickname,
            'brand_id' => $request->brand_id,
            'vehicle_model_id' => $request->vehicle_model_id,
            'year' => $request->year,
            'plate' => $request->plate,
            'color' => $request->color,
            'fuel_type' => $request->fuel_type,
            'current_mileage' => $request->current_mileage,
        ]);

        $vehicle->load(['brand', 'vehicleModel']);

        return response()->json([
            'message' => 'Veículo atualizado com sucesso.',
            'data' => new VehicleResource($vehicle),
        ]);
    }

    /**
     * Delete a vehicle.
     */
    public function destroy(Request $request, Vehicle $vehicle): JsonResponse
    {
        if ($vehicle->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $vehicle->delete();

        return response()->json([
            'message' => 'Veículo excluído com sucesso.',
        ]);
    }
}
