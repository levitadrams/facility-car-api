<?php

namespace Database\Factories;

use App\Models\Maintenance;
use App\Models\MaintenanceType;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Maintenance>
 */
class MaintenanceFactory extends Factory
{
    protected $model = Maintenance::class;

    public function definition(): array
    {
        return [
            'user_id' => 1,
            'vehicle_id' => Vehicle::factory(),
            'maintenance_type_id' => MaintenanceType::factory(),
            'description' => fake()->optional()->sentence(),
            'performed_at' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'current_mileage' => fake()->numberBetween(1000, 200000),
            'cost' => fake()->randomFloat(2, 50, 5000),
            'workshop_name' => fake()->optional()->company(),
            'invoice_number' => fake()->optional()->bothify('NF-#######'),
            'notes' => fake()->optional()->paragraph(),
            'next_maintenance_mileage' => fake()->optional()->numberBetween(1000, 200000),
            'next_maintenance_date' => fake()->optional()->dateTimeBetween('now', '+1 year')?->format('Y-m-d'),
        ];
    }
}
