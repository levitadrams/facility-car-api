<?php

namespace Database\Factories;

use App\Models\MaintenanceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceCategory>
 */
class MaintenanceCategoryFactory extends Factory
{
    protected $model = MaintenanceCategory::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'icon' => fake()->optional()->word(),
            'color' => fake()->optional()->hexColor(),
            'active' => true,
        ];
    }
}
