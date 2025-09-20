<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Infrastructure\Persistence\Eloquent\Tenants\TenantModel;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin global
        User::updateOrCreate(
            ['email' => 'admin@laravel-saas.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'tenant_id' => null,
            ]
        );

        // Usuarios por tenant activo (3 por tenant)
        $tenants = TenantModel::where('status', 'active')->get();
        foreach ($tenants as $tenant) {
            for ($i = 1; $i <= 3; $i++) {
                $email = sprintf('user%d_%s@laravel-saas.com', $i, $tenant->slug ?: ('tenant'.$tenant->id));
                User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => 'User '.$i.' '.$tenant->name,
                        'password' => Hash::make('password'),
                        'role' => 'user',
                        'tenant_id' => $tenant->id,
                    ]
                );
            }
        }
    }
}
