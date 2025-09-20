<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// ===== Interfaces (Dominio) =====
use App\Domain\Plans\Repositories\PlanRepositoryInterface;
use App\Domain\Subscriptions\Repositories\SubscriptionRepositoryInterface;
// (si luego agregas Tenants, Users, etc., los importas aquí)

// ===== Implementaciones (Infraestructura / Eloquent) =====
use App\Infrastructure\Persistence\Eloquent\Plans\PlanRepository;
use App\Infrastructure\Persistence\Eloquent\Subscriptions\SubscriptionRepository;
// (agrega las implementaciones nuevas cuando existan)

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
        // Plans
        $this->app->bind(PlanRepositoryInterface::class, PlanRepository::class);

        // Subscriptions
        $this->app->bind(SubscriptionRepositoryInterface::class, SubscriptionRepository::class);

      }

    public function boot(): void
    {
        // nada por ahora
    }
}
