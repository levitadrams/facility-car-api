<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'maintenance_type_id',
        'description',
        'performed_at',
        'current_mileage',
        'cost',
        'workshop_name',
        'invoice_number',
        'notes',
        'next_maintenance_mileage',
        'next_maintenance_date',
    ];

    protected function casts(): array
    {
        return [
            'performed_at' => 'date',
            'current_mileage' => 'integer',
            'cost' => 'float',
            'next_maintenance_mileage' => 'integer',
            'next_maintenance_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function maintenanceType(): BelongsTo
    {
        return $this->belongsTo(MaintenanceType::class);
    }
}
