<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Maintenance;
use App\Models\MaintenanceCategory;
use App\Models\MaintenanceType;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;
    private Vehicle $vehicle;
    private MaintenanceType $maintenanceType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test_token')->plainTextToken;

        $brand = Brand::factory()->create();
        $model = VehicleModel::factory()->for($brand)->create();
        $this->vehicle = Vehicle::factory()->create([
            'user_id' => $this->user->id,
            'brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
        ]);

        $category = MaintenanceCategory::factory()->create(['name' => 'Motor']);
        $this->maintenanceType = MaintenanceType::factory()->create([
            'maintenance_category_id' => $category->id,
            'name' => 'Troca de Óleo',
        ]);
    }

    private function authHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ];
    }

    private function maintenancePayload(array $overrides = []): array
    {
        return array_merge([
            'vehicle_id' => $this->vehicle->id,
            'maintenance_type_id' => $this->maintenanceType->id,
            'description' => 'Troca de óleo e filtro',
            'performed_at' => '2026-06-15',
            'current_mileage' => 120000,
            'cost' => 280.00,
            'workshop_name' => 'Oficina do Zé',
            'invoice_number' => 'NF-123456',
            'notes' => 'Usado óleo sintético',
        ], $overrides);
    }

    public function test_can_list_user_maintenances(): void
    {
        Maintenance::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
        ]);

        $response = $this->getJson('/api/maintenances', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'vehicle', 'maintenance_type', 'performed_at', 'current_mileage', 'cost'],
                ],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    public function test_cannot_list_other_user_maintenances(): void
    {
        $otherUser = User::factory()->create();
        $otherVehicle = Vehicle::factory()->create(['user_id' => $otherUser->id]);
        Maintenance::factory()->count(2)->create([
            'user_id' => $otherUser->id,
            'vehicle_id' => $otherVehicle->id,
        ]);

        $response = $this->getJson('/api/maintenances', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_can_filter_by_vehicle(): void
    {
        $otherVehicle = Vehicle::factory()->create(['user_id' => $this->user->id]);
        Maintenance::factory()->create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
        ]);
        Maintenance::factory()->create([
            'user_id' => $this->user->id,
            'vehicle_id' => $otherVehicle->id,
        ]);

        $response = $this->getJson("/api/maintenances?vehicle_id={$this->vehicle->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.vehicle.id', $this->vehicle->id);
    }

    public function test_can_filter_by_maintenance_type(): void
    {
        Maintenance::factory()->create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'maintenance_type_id' => $this->maintenanceType->id,
        ]);
        $otherType = MaintenanceType::factory()->create([
            'maintenance_category_id' => $this->maintenanceType->maintenance_category_id,
            'name' => 'Alinhamento',
        ]);
        Maintenance::factory()->create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'maintenance_type_id' => $otherType->id,
        ]);

        $response = $this->getJson("/api/maintenances?maintenance_type_id={$this->maintenanceType->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.maintenance_type_id', $this->maintenanceType->id);
    }

    public function test_can_filter_by_date_range(): void
    {
        Maintenance::factory()->create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'performed_at' => '2026-01-15',
        ]);
        Maintenance::factory()->create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'performed_at' => '2026-06-20',
        ]);
        Maintenance::factory()->create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'performed_at' => '2025-12-01',
        ]);

        $response = $this->getJson('/api/maintenances?start_date=2026-01-01&end_date=2026-12-31', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_create_maintenance(): void
    {
        $payload = $this->maintenancePayload();

        $response = $this->postJson('/api/maintenances', $payload, $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['maintenance_type_id' => $this->maintenanceType->id, 'current_mileage' => 120000]);

        $this->assertDatabaseHas('maintenances', [
            'vehicle_id' => $this->vehicle->id,
            'maintenance_type_id' => $this->maintenanceType->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_cannot_create_maintenance_without_required_fields(): void
    {
        $response = $this->postJson('/api/maintenances', [], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['vehicle_id', 'maintenance_type_id', 'performed_at', 'current_mileage', 'cost']);
    }

    public function test_cannot_create_maintenance_for_other_user_vehicle(): void
    {
        $otherUser = User::factory()->create();
        $otherVehicle = Vehicle::factory()->create(['user_id' => $otherUser->id]);

        $payload = $this->maintenancePayload(['vehicle_id' => $otherVehicle->id]);

        $response = $this->postJson('/api/maintenances', $payload, $this->authHeaders());

        $response->assertStatus(404);
    }

    public function test_can_show_own_maintenance(): void
    {
        $maintenance = Maintenance::factory()->create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
        ]);

        $response = $this->getJson("/api/maintenances/{$maintenance->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $maintenance->id);
    }

    public function test_cannot_show_other_user_maintenance(): void
    {
        $otherUser = User::factory()->create();
        $otherVehicle = Vehicle::factory()->create(['user_id' => $otherUser->id]);
        $maintenance = Maintenance::factory()->create([
            'user_id' => $otherUser->id,
            'vehicle_id' => $otherVehicle->id,
        ]);

        $response = $this->getJson("/api/maintenances/{$maintenance->id}", $this->authHeaders());

        $response->assertStatus(403);
    }

    public function test_can_update_own_maintenance(): void
    {
        $maintenance = Maintenance::factory()->create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
        ]);

        $otherType = MaintenanceType::factory()->create([
            'maintenance_category_id' => $this->maintenanceType->maintenance_category_id,
            'name' => 'Revisão Geral',
        ]);

        $payload = $this->maintenancePayload([
            'maintenance_type_id' => $otherType->id,
            'cost' => 850.00,
        ]);

        $response = $this->putJson("/api/maintenances/{$maintenance->id}", $payload, $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['maintenance_type_id' => $otherType->id]);
    }

    public function test_cannot_update_other_user_maintenance(): void
    {
        $otherUser = User::factory()->create();
        $otherVehicle = Vehicle::factory()->create(['user_id' => $otherUser->id]);
        $maintenance = Maintenance::factory()->create([
            'user_id' => $otherUser->id,
            'vehicle_id' => $otherVehicle->id,
        ]);

        $payload = $this->maintenancePayload(['vehicle_id' => $otherVehicle->id]);

        $response = $this->putJson("/api/maintenances/{$maintenance->id}", $payload, $this->authHeaders());

        $response->assertStatus(403);
    }

    public function test_can_delete_own_maintenance(): void
    {
        $maintenance = Maintenance::factory()->create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
        ]);

        $response = $this->deleteJson("/api/maintenances/{$maintenance->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('maintenances', ['id' => $maintenance->id]);
    }

    public function test_cannot_delete_other_user_maintenance(): void
    {
        $otherUser = User::factory()->create();
        $otherVehicle = Vehicle::factory()->create(['user_id' => $otherUser->id]);
        $maintenance = Maintenance::factory()->create([
            'user_id' => $otherUser->id,
            'vehicle_id' => $otherVehicle->id,
        ]);

        $response = $this->deleteJson("/api/maintenances/{$maintenance->id}", [], $this->authHeaders());

        $response->assertStatus(403);
        $this->assertDatabaseHas('maintenances', ['id' => $maintenance->id]);
    }

    public function test_can_create_maintenance_with_next_maintenance_fields(): void
    {
        $payload = $this->maintenancePayload([
            'next_maintenance_mileage' => 130000,
            'next_maintenance_date' => '2026-12-15',
        ]);

        $response = $this->postJson('/api/maintenances', $payload, $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonPath('data.next_maintenance_mileage', 130000)
            ->assertJsonPath('data.next_maintenance_date', '2026-12-15');
    }
}
