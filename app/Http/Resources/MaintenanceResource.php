<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vehicle' => $this->whenLoaded('vehicle', fn () => [
                'id' => $this->vehicle->id,
                'nickname' => $this->vehicle->nickname,
                'plate' => $this->vehicle->plate,
                'brand' => $this->vehicle->brand ? [
                    'id' => $this->vehicle->brand->id,
                    'name' => $this->vehicle->brand->name,
                ] : null,
                'model' => $this->vehicle->vehicleModel ? [
                    'id' => $this->vehicle->vehicleModel->id,
                    'name' => $this->vehicle->vehicleModel->name,
                ] : null,
            ]),
            'maintenance_type_id' => $this->maintenance_type_id,
            'maintenance_type' => $this->whenLoaded('maintenanceType', fn () => [
                'id' => $this->maintenanceType->id,
                'name' => $this->maintenanceType->name,
                'category' => $this->maintenanceType->maintenanceCategory ? [
                    'id' => $this->maintenanceType->maintenanceCategory->id,
                    'name' => $this->maintenanceType->maintenanceCategory->name,
                    'color' => $this->maintenanceType->maintenanceCategory->color,
                ] : null,
            ]),
            'description' => $this->description,
            'performed_at' => $this->performed_at?->toDateString(),
            'current_mileage' => $this->current_mileage,
            'cost' => $this->cost,
            'workshop_name' => $this->workshop_name,
            'invoice_number' => $this->invoice_number,
            'notes' => $this->notes,
            'next_maintenance_mileage' => $this->next_maintenance_mileage,
            'next_maintenance_date' => $this->next_maintenance_date?->toDateString(),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
