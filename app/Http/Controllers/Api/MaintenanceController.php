<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Maintenance\StoreMaintenanceRequest;
use App\Http\Requests\Maintenance\UpdateMaintenanceRequest;
use App\Http\Resources\MaintenanceResource;
use App\Models\Maintenance;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    /**
     * List all maintenances for the authenticated user with filters, pagination and sorting.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Maintenance::with(['vehicle.brand', 'vehicle.vehicleModel', 'maintenanceType.maintenanceCategory'])
            ->where('user_id', $request->user()->id);

        // Filter by vehicle
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->input('vehicle_id'));
        }

        // Filter by maintenance type
        if ($request->filled('maintenance_type_id')) {
            $query->where('maintenance_type_id', $request->input('maintenance_type_id'));
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('performed_at', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('performed_at', '<=', $request->input('end_date'));
        }

        // Sorting: most recent first by default
        $query->orderBy('performed_at', 'desc');

        $maintenances = $query->paginate(15);

        return response()->json([
            'data' => MaintenanceResource::collection($maintenances),
            'meta' => [
                'current_page' => $maintenances->currentPage(),
                'last_page' => $maintenances->lastPage(),
                'per_page' => $maintenances->perPage(),
                'total' => $maintenances->total(),
            ],
        ]);
    }

    /**
     * Store a new maintenance.
     */
    public function store(StoreMaintenanceRequest $request): JsonResponse
    {
        // Ensure the vehicle belongs to the authenticated user
        $vehicle = Vehicle::where('id', $request->vehicle_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $vehicle) {
            return response()->json(['message' => 'Veículo não encontrado.'], 404);
        }

        $maintenance = Maintenance::create([
            'user_id' => $request->user()->id,
            'vehicle_id' => $request->vehicle_id,
            'maintenance_type_id' => $request->maintenance_type_id,
            'description' => $request->description,
            'performed_at' => $request->performed_at,
            'current_mileage' => $request->current_mileage,
            'cost' => $request->cost,
            'workshop_name' => $request->workshop_name,
            'invoice_number' => $request->invoice_number,
            'notes' => $request->notes,
            'next_maintenance_mileage' => $request->next_maintenance_mileage,
            'next_maintenance_date' => $request->next_maintenance_date,
        ]);

        $maintenance->load(['vehicle.brand', 'vehicle.vehicleModel', 'maintenanceType.maintenanceCategory']);

        return response()->json([
            'message' => 'Manutenção cadastrada com sucesso.',
            'data' => new MaintenanceResource($maintenance),
        ], 201);
    }

    /**
     * Show a specific maintenance.
     */
    public function show(Request $request, Maintenance $maintenance): JsonResponse
    {
        if ($maintenance->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $maintenance->load(['vehicle.brand', 'vehicle.vehicleModel', 'maintenanceType.maintenanceCategory']);

        return response()->json([
            'data' => new MaintenanceResource($maintenance),
        ]);
    }

    /**
     * Update a maintenance.
     */
    public function update(UpdateMaintenanceRequest $request, Maintenance $maintenance): JsonResponse
    {
        if ($maintenance->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        // Ensure the new vehicle belongs to the authenticated user
        $vehicle = Vehicle::where('id', $request->vehicle_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $vehicle) {
            return response()->json(['message' => 'Veículo não encontrado.'], 404);
        }

        $maintenance->update([
            'vehicle_id' => $request->vehicle_id,
            'maintenance_type_id' => $request->maintenance_type_id,
            'description' => $request->description,
            'performed_at' => $request->performed_at,
            'current_mileage' => $request->current_mileage,
            'cost' => $request->cost,
            'workshop_name' => $request->workshop_name,
            'invoice_number' => $request->invoice_number,
            'notes' => $request->notes,
            'next_maintenance_mileage' => $request->next_maintenance_mileage,
            'next_maintenance_date' => $request->next_maintenance_date,
        ]);

        $maintenance->load(['vehicle.brand', 'vehicle.vehicleModel', 'maintenanceType.maintenanceCategory']);

        return response()->json([
            'message' => 'Manutenção atualizada com sucesso.',
            'data' => new MaintenanceResource($maintenance),
        ]);
    }

    /**
     * Delete a maintenance.
     */
    public function destroy(Request $request, Maintenance $maintenance): JsonResponse
    {
        if ($maintenance->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $maintenance->delete();

        return response()->json([
            'message' => 'Manutenção excluída com sucesso.',
        ]);
    }
}
