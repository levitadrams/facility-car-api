<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use App\Models\RouteDestination;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Return dashboard statistics for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $vehiclesCount = Vehicle::where('user_id', $userId)->count();

        $maintenancesCount = Maintenance::where('user_id', $userId)->count();

        $destinationsCount = RouteDestination::where('user_id', $userId)
            ->where('active', true)
            ->count();

        $currentMonthCost = Maintenance::where('user_id', $userId)
            ->whereMonth('performed_at', now()->month)
            ->whereYear('performed_at', now()->year)
            ->sum('cost');

        return response()->json([
            'data' => [
                'vehicles_count' => $vehiclesCount,
                'maintenances_count' => $maintenancesCount,
                'destinations_count' => $destinationsCount,
                'current_month_maintenance_cost' => (float) $currentMonthCost,
            ],
        ]);
    }
}
