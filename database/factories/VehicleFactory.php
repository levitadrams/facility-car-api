<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        $brand = Brand::factory()->create();
        $model = VehicleModel::factory()->for($brand)->create();

        return [
            'user_id' => 1,
            'nickname' => fake()->optional()->word(),
            'brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
            'year' => fake()->numberBetween(2015, 2025),
            'plate' => fake()->regexify('[A-Z]{3}[0-9][A-Z][0-9]{2}'),
            'color' => fake()->optional()->safeColorName(),
            'fuel_type' => fake()->optional()->randomElement(['Gasolina', 'Etanol', 'Diesel', 'Flex', 'Elétrico']),
            'current_mileage' => fake()->numberBetween(0, 200000),
        ];
    }
}
