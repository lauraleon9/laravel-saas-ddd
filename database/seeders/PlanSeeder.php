<?php

namespace Database\Seeders;

use App\Infrastructure\Persistence\Eloquent\Plans\PlanModel;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'monthly_price' => 9.99,
                'user_limit' => 5,
                'features' => json_encode([
                    'storage' => '10GB',
                    'support' => 'Email',
                    'integrations' => 'Basic',
                    'reports' => false
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Professional',
                'monthly_price' => 29.99,
                'user_limit' => 25,
                'features' => json_encode([
                    'storage' => '100GB',
                    'support' => 'Priority Email',
                    'integrations' => 'Advanced',
                    'reports' => true,
                    'custom_branding' => false
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'monthly_price' => 99.99,
                'user_limit' => 100,
                'features' => json_encode([
                    'storage' => '1TB',
                    'support' => '24/7 Phone & Email',
                    'integrations' => 'Premium',
                    'reports' => true,
                    'custom_branding' => true,
                    'api_access' => true,
                    'sso' => true
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Startup (Legacy)',
                'monthly_price' => 19.99,
                'user_limit' => 10,
                'features' => json_encode([
                    'storage' => '50GB',
                    'support' => 'Email',
                    'integrations' => 'Basic'
                ]),
                'is_active' => false, // Plan descontinuado
            ],
        ];

        foreach ($plans as $plan) {
            PlanModel::create($plan);
        }
    }
}
