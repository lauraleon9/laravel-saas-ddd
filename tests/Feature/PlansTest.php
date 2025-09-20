<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Eloquent\Plans\PlanModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlansTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdminUser()
    {
        return User::factory()->create([
            'email' => 'admin@test.com',
            'role' => 'admin',
            'tenant_id' => null
        ]);
    }

    public function test_can_list_plans()
    {
        // Crear algunos planes de prueba
        PlanModel::create([
            'name' => 'Basic',
            'monthly_price' => 10.00,
            'user_limit' => 5,
            'features' => null,
            'is_active' => true
        ]);

        PlanModel::create([
            'name' => 'Premium',
            'monthly_price' => 25.00,
            'user_limit' => 15,
            'features' => '{"storage": "100GB"}',
            'is_active' => true
        ]);

        // Plan inactivo - no debe aparecer
        PlanModel::create([
            'name' => 'Inactive',
            'monthly_price' => 5.00,
            'user_limit' => 1,
            'features' => null,
            'is_active' => false
        ]);

        $response = $this->getJson('/api/v1/plans');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'monthly_price',
                            'user_limit',
                            'features',
                            'is_active',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'meta' => ['total']
                ])
                ->assertJsonCount(2, 'data') // Solo 2 activos
                ->assertJsonFragment(['name' => 'Basic'])
                ->assertJsonFragment(['name' => 'Premium'])
                ->assertJsonMissing(['name' => 'Inactive']);
    }

    public function test_can_create_plan()
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $planData = [
            'name' => 'Enterprise',
            'monthly_price' => 99.99,
            'user_limit' => 100,
            'features' => '{"storage": "unlimited", "priority_support": true}'
        ];

        $response = $this->postJson('/api/v1/plans', $planData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'monthly_price',
                        'user_limit',
                        'features',
                        'is_active'
                    ],
                    'message'
                ])
                ->assertJsonFragment([
                    'name' => 'Enterprise',
                    'monthly_price' => '99.99',
                    'user_limit' => 100,
                    'is_active' => true
                ]);

        // Verificar que se guardó en la base de datos
        $this->assertDatabaseHas('plans', [
            'name' => 'Enterprise',
            'monthly_price' => 99.99,
            'user_limit' => 100,
            'is_active' => true
        ]);
    }

    public function test_can_show_plan()
    {
        $plan = PlanModel::create([
            'name' => 'Show Test',
            'monthly_price' => 15.50,
            'user_limit' => 10,
            'features' => '{"feature1": "value1"}',
            'is_active' => true
        ]);

        $response = $this->getJson("/api/v1/plans/{$plan->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'monthly_price',
                        'user_limit',
                        'features',
                        'is_active'
                    ]
                ])
                ->assertJsonFragment([
                    'id' => $plan->id,
                    'name' => 'Show Test',
                    'monthly_price' => '15.50'
                ]);
    }

    public function test_can_update_plan()
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $plan = PlanModel::create([
            'name' => 'Update Test',
            'monthly_price' => 20.00,
            'user_limit' => 8,
            'features' => null,
            'is_active' => true
        ]);

        $updateData = [
            'name' => 'Updated Plan',
            'monthly_price' => 25.00,
            'user_limit' => 12
        ];

        $response = $this->putJson("/api/v1/plans/{$plan->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonFragment([
                    'name' => 'Updated Plan',
                    'monthly_price' => '25.00',
                    'user_limit' => 12
                ])
                ->assertJsonStructure([
                    'data',
                    'message'
                ]);

        // Verificar en la base de datos
        $this->assertDatabaseHas('plans', [
            'id' => $plan->id,
            'name' => 'Updated Plan',
            'monthly_price' => 25.00,
            'user_limit' => 12
        ]);
    }

    public function test_can_deactivate_plan()
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $plan = PlanModel::create([
            'name' => 'To Deactivate',
            'monthly_price' => 10.00,
            'user_limit' => 5,
            'features' => null,
            'is_active' => true
        ]);

        $response = $this->deleteJson("/api/v1/plans/{$plan->id}");

        $response->assertStatus(200)
                ->assertJsonFragment([
                    'message' => 'Plan deactivated successfully'
                ]);

        // Verificar que se marcó como inactivo, no eliminado
        $this->assertDatabaseHas('plans', [
            'id' => $plan->id,
            'name' => 'To Deactivate',
            'is_active' => false
        ]);
    }

    public function test_validation_errors_when_creating_plan()
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        // Test sin nombre
        $response = $this->postJson('/api/v1/plans', [
            'monthly_price' => 10.00,
            'user_limit' => 5
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);

        // Test con precio negativo
        $response = $this->postJson('/api/v1/plans', [
            'name' => 'Test Plan',
            'monthly_price' => -5.00,
            'user_limit' => 5
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['monthly_price']);

        // Test con limite de usuarios inválido
        $response = $this->postJson('/api/v1/plans', [
            'name' => 'Test Plan',
            'monthly_price' => 10.00,
            'user_limit' => 0
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['user_limit']);
    }

    public function test_404_when_plan_not_found()
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $response = $this->getJson('/api/v1/plans/999');
        $response->assertStatus(404);

        $response = $this->putJson('/api/v1/plans/999', [
            'name' => 'Test'
        ]);
        $response->assertStatus(404);

        $response = $this->deleteJson('/api/v1/plans/999');
        $response->assertStatus(404);
    }
}