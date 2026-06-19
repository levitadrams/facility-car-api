<?php

namespace Database\Factories;

use App\Models\MaintenanceCategory;
use App\Models\MaintenanceType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceType>
 */
class MaintenanceTypeFactory extends Factory
{
    protected $model = MaintenanceType::class;

    public function definition(): array
    {
        return [
            'maintenance_category_id' => MaintenanceCategory::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'recommended_interval_km' => fake()->optional()->numberBetween(5000, 100000),
            'recommended_interval_months' => fake()->optional()->numberBetween(6, 60),
            'active' => true,
        ];
    }
}
