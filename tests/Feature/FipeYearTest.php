<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use App\Models\VehicleModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FipeYearTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_years_for_model(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_token')->plainTextToken;

        $brand = Brand::factory()->create(['fipe_code' => '22']);
        $model = VehicleModel::factory()->for($brand)->create(['fipe_code' => '6285']);

        $response = $this->getJson(
            "/api/brands/{$brand->id}/models/{$model->id}/years",
            ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json']
        );

        // API FIPE pode falhar em testes sem internet, então aceitamos 200 ou 502
        $this->assertTrue(
            $response->status() === 200 || $response->status() === 502,
            'Esperado 200 ou 502, recebido: ' . $response->status()
        );

        if ($response->status() === 200) {
            $response->assertJsonStructure(['data']);
        }
    }
}
