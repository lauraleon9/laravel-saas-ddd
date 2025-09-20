<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar seeders en orden correcto debido a las dependencias
        $this->call([
            PlanSeeder::class,        // Primero los planes
            TenantSeeder::class,      // Luego los tenants (necesitan planes para suscripciones)
            UserSeeder::class,        // Finalmente los usuarios (necesitan tenants)
        ]);
    }
}
