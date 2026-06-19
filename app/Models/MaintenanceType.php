<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'maintenance_category_id',
        'name',
        'description',
        'recommended_interval_km',
        'recommended_interval_months',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'recommended_interval_km' => 'integer',
            'recommended_interval_months' => 'integer',
            'active' => 'boolean',
        ];
    }

    public function maintenanceCategory(): BelongsTo
    {
        return $this->belongsTo(MaintenanceCategory::class);
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }
}
