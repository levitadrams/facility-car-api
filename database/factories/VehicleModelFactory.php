<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\VehicleModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VehicleModel>
 */
class VehicleModelFactory extends Factory
{
    protected $model = VehicleModel::class;

    public function definition(): array
    {
        return [
            'brand_id' => Brand::factory(),
            'name' => fake()->word(),
            'fipe_code' => fake()->unique()->numerify('####'),
        ];
    }
}
