<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test_token')->plainTextToken;
    }

    private function authHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ];
    }

    public function test_can_list_user_vehicles(): void
    {
        Vehicle::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/vehicles', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'brand', 'model', 'year', 'plate'],
                ],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    public function test_cannot_list_other_user_vehicles(): void
    {
        $otherUser = User::factory()->create();
        Vehicle::factory()->count(2)->create(['user_id' => $otherUser->id]);

        $response = $this->getJson('/api/vehicles', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_can_create_vehicle(): void
    {
        $brand = Brand::factory()->create();
        $model = VehicleModel::factory()->for($brand)->create();

        $data = [
            'brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
            'year' => 2022,
            'plate' => 'ABC1D23',
            'color' => 'Prata',
            'fuel_type' => 'Gasolina',
            'current_mileage' => 120000,
        ];

        $response = $this->postJson('/api/vehicles', $data, $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['brand_id' => $brand->id, 'vehicle_model_id' => $model->id]);

        $this->assertDatabaseHas('vehicles', [
            'plate' => 'ABC1D23',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_cannot_create_vehicle_without_required_fields(): void
    {
        $response = $this->postJson('/api/vehicles', [], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['brand_id', 'vehicle_model_id', 'year', 'plate', 'current_mileage']);
    }

    public function test_cannot_create_vehicle_with_invalid_plate(): void
    {
        $brand = Brand::factory()->create();
        $model = VehicleModel::factory()->for($brand)->create();

        $data = [
            'brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
            'year' => 2022,
            'plate' => 'INVALID',
            'current_mileage' => 0,
        ];

        $response = $this->postJson('/api/vehicles', $data, $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['plate']);
    }

    public function test_cannot_create_vehicle_with_negative_mileage(): void
    {
        $brand = Brand::factory()->create();
        $model = VehicleModel::factory()->for($brand)->create();

        $data = [
            'brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
            'year' => 2022,
            'plate' => 'ABC1D23',
            'current_mileage' => -100,
        ];

        $response = $this->postJson('/api/vehicles', $data, $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_mileage']);
    }

    public function test_cannot_create_vehicle_with_year_below_1980(): void
    {
        $brand = Brand::factory()->create();
        $model = VehicleModel::factory()->for($brand)->create();

        $data = [
            'brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
            'year' => 1970,
            'plate' => 'ABC1D23',
            'current_mileage' => 0,
        ];

        $response = $this->postJson('/api/vehicles', $data, $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['year']);
    }

    public function test_cannot_create_vehicle_with_duplicate_plate(): void
    {
        Vehicle::factory()->create(['user_id' => $this->user->id, 'plate' => 'ABC1D23']);

        $brand = Brand::factory()->create();
        $model = VehicleModel::factory()->for($brand)->create();

        $data = [
            'brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
            'year' => 2023,
            'plate' => 'ABC1D23',
            'current_mileage' => 0,
        ];

        $response = $this->postJson('/api/vehicles', $data, $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['plate']);
    }

    public function test_can_show_own_vehicle(): void
    {
        $vehicle = Vehicle::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/vehicles/{$vehicle->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $vehicle->id);
    }

    public function test_cannot_show_other_user_vehicle(): void
    {
        $otherUser = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/vehicles/{$vehicle->id}", $this->authHeaders());

        $response->assertStatus(403);
    }

    public function test_can_update_own_vehicle(): void
    {
        $vehicle = Vehicle::factory()->create(['user_id' => $this->user->id]);
        $newBrand = Brand::factory()->create();
        $newModel = VehicleModel::factory()->for($newBrand)->create();

        $data = [
            'brand_id' => $newBrand->id,
            'vehicle_model_id' => $newModel->id,
            'year' => 2023,
            'plate' => $vehicle->plate,
            'current_mileage' => 50000,
        ];

        $response = $this->putJson("/api/vehicles/{$vehicle->id}", $data, $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['brand_id' => $newBrand->id, 'vehicle_model_id' => $newModel->id]);
    }

    public function test_cannot_update_other_user_vehicle(): void
    {
        $otherUser = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $otherUser->id]);
        $brand = Brand::factory()->create();
        $model = VehicleModel::factory()->for($brand)->create();

        $data = [
            'brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
            'year' => 2023,
            'plate' => $vehicle->plate,
            'current_mileage' => 0,
        ];

        $response = $this->putJson("/api/vehicles/{$vehicle->id}", $data, $this->authHeaders());

        $response->assertStatus(403);
    }

    public function test_can_delete_own_vehicle(): void
    {
        $vehicle = Vehicle::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/vehicles/{$vehicle->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('vehicles', ['id' => $vehicle->id]);
    }

    public function test_cannot_delete_other_user_vehicle(): void
    {
        $otherUser = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->deleteJson("/api/vehicles/{$vehicle->id}", [], $this->authHeaders());

        $response->assertStatus(403);
        $this->assertDatabaseHas('vehicles', ['id' => $vehicle->id]);
    }

    public function test_can_search_vehicles_by_plate(): void
    {
        Vehicle::factory()->create(['user_id' => $this->user->id, 'plate' => 'ABC1D23']);
        Vehicle::factory()->create(['user_id' => $this->user->id, 'plate' => 'XYZ9K87']);

        $response = $this->getJson('/api/vehicles?search=ABC', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.plate', 'ABC1D23');
    }

    public function test_can_sort_vehicles_by_mileage(): void
    {
        Vehicle::factory()->create(['user_id' => $this->user->id, 'current_mileage' => 1000]);
        Vehicle::factory()->create(['user_id' => $this->user->id, 'current_mileage' => 50000]);

        $response = $this->getJson('/api/vehicles?sort=mileage', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonPath('data.0.current_mileage', 50000);
    }
}
