<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon,
            'color' => $this->color,
            'active' => $this->active,
            'types' => $this->whenLoaded('maintenanceTypes', fn () =>
                MaintenanceTypeResource::collection($this->maintenanceTypes)
            ),
        ];
    }
}
