<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RouteDestination\StoreRequest;
use App\Http\Requests\RouteDestination\RouteMapRequest;
use App\Http\Resources\RouteMapResource;
use App\Models\RouteDestination;
use App\Services\OsrmService;
use App\Services\RouteMapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RouteDestinationController extends Controller
{
    /**
     * List all destinations for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $destinations = RouteDestination::where('user_id', $request->user()->id)
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return response()->json($destinations);
    }

    /**
     * Store a new destination
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $destination = RouteDestination::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'notes' => $request->notes,
            'active' => $request->boolean('active', true),
        ]);

        return response()->json($destination, 201);
    }

    /**
     * Show a specific destination
     */
    public function show(Request $request, RouteDestination $destination): JsonResponse
    {
        if ($destination->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        return response()->json($destination);
    }

    /**
     * Update a destination
     */
    public function update(StoreRequest $request, RouteDestination $destination): JsonResponse
    {
        if ($destination->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $destination->update([
            'name' => $request->name,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'notes' => $request->notes,
            'active' => $request->boolean('active', $destination->active),
        ]);

        return response()->json($destination);
    }

    /**
     * Soft delete (deactivate) a destination
     */
    public function destroy(Request $request, RouteDestination $destination): JsonResponse
    {
        if ($destination->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $destination->update(['active' => false]);

        return response()->json(['message' => 'Destino removido com sucesso.']);
    }

    /**
     * Calcula rota entre dois pontos usando OSRM
     * 
     * POST /api/destinations/calculate-route
     * Body: {
     *   "origin_lat": -22.9083,
     *   "origin_lon": -43.1964,
     *   "dest_lat": -22.9068,
     *   "dest_lon": -43.1729
     * }
     */
    public function calculateRoute(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'origin_lat' => 'required|numeric|between:-90,90',
            'origin_lon' => 'required|numeric|between:-180,180',
            'dest_lat' => 'required|numeric|between:-90,90',
            'dest_lon' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dados inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $osrmService = new OsrmService();
            
            $result = $osrmService->calculateRoute(
                $request->origin_lat,
                $request->origin_lon,
                $request->dest_lat,
                $request->dest_lon
            );

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao calcular rota: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retorna rota detalhada (com geometria) entre localização atual e destino
     *
     * GET /api/destinations/{id}/route?latitude=-22.90&longitude=-43.20
     */
    public function route(Request $request, RouteDestination $destination): JsonResponse
    {
        // Valida propriedade do usuário
        if ($destination->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        // Valida coordenadas do destino
        if ($destination->latitude === null || $destination->longitude === null) {
            return response()->json([
                'message' => 'Destino não possui coordenadas válidas.',
            ], 422);
        }

        // Valida latitude/longitude da origem
        $validator = Validator::make($request->query(), [
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Coordenadas de origem inválidas',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $originLat  = (float) $request->query('latitude');
        $originLon  = (float) $request->query('longitude');
        $destLat    = (float) $destination->latitude;
        $destLon    = (float) $destination->longitude;

        try {
            $routeMapService = new RouteMapService();

            $routeData = $routeMapService->calculateDetailedRoute(
                $originLat,
                $originLon,
                $destLat,
                $destLon
            );

            // Anexa origem e destino ao resultado para o resource
            $routeData['origin'] = [
                'latitude'  => $originLat,
                'longitude' => $originLon,
            ];
            $routeData['destination'] = [
                'latitude'  => $destLat,
                'longitude' => $destLon,
                'name'      => $destination->name,
            ];

            return response()->json([
                'success' => true,
                'data'    => new RouteMapResource($routeData),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
