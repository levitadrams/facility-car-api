<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'maintenance_category_id' => $this->maintenance_category_id,
            'name' => $this->name,
            'description' => $this->description,
            'recommended_interval_km' => $this->recommended_interval_km,
            'recommended_interval_months' => $this->recommended_interval_months,
            'active' => $this->active,
            'category' => $this->whenLoaded('maintenanceCategory', fn () => [
                'id' => $this->maintenanceCategory->id,
                'name' => $this->maintenanceCategory->name,
                'color' => $this->maintenanceCategory->color,
            ]),
        ];
    }
}
