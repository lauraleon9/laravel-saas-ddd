<?php

namespace Database\Seeders;

use App\Infrastructure\Persistence\Eloquent\Plans\PlanModel;
use App\Infrastructure\Persistence\Eloquent\Subscriptions\SubscriptionModel;
use App\Infrastructure\Persistence\Eloquent\Tenants\TenantModel;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = PlanModel::where('is_active', true)->get();
        
        $tenants = [
            [
                'name' => 'TechCorp Solutions',
                'slug' => 'techcorp-solutions',
                'status' => 'active',
                'plan_name' => 'Professional'
            ],
            [
                'name' => 'Innovate Industries',
                'slug' => 'innovate-industries',
                'status' => 'active',
                'plan_name' => 'Enterprise'
            ],
            [
                'name' => 'StartupXYZ',
                'slug' => 'startupxyz',
                'status' => 'active',
                'plan_name' => 'Basic'
            ],
            [
                'name' => 'Global Services Inc',
                'slug' => 'global-services-inc',
                'status' => 'active',
                'plan_name' => 'Enterprise'
            ],
            [
                'name' => 'Small Business Co',
                'slug' => 'small-business-co',
                'status' => 'inactive',
                'plan_name' => 'Basic'
            ],
        ];

        foreach ($tenants as $tenantData) {
            // Crear el tenant
            $tenant = TenantModel::create([
                'name' => $tenantData['name'],
                'slug' => $tenantData['slug'],
                'status' => $tenantData['status'],
            ]);

            // Asignar una suscripción activa si el tenant está activo
            if ($tenantData['status'] === 'active') {
                $plan = $plans->where('name', $tenantData['plan_name'])->first();
                
                if ($plan) {
                    SubscriptionModel::create([
                        'tenant_id' => $tenant->id,
                        'plan_id' => $plan->id,
                        'starts_at' => now()->subDays(rand(1, 30)),
                        'ends_at' => null,
                        'is_active' => true,
                        'price_at_signup' => $plan->monthly_price,
                        'user_limit_snapshot' => $plan->user_limit,
                        'features_snapshot' => $plan->features,
                    ]);
                }
            }
        }
    }
}
