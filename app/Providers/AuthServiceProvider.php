<?php

namespace App\Providers;

use App\Infrastructure\Persistence\Eloquent\Plans\PlanModel;
use App\Policies\PlanPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        PlanModel::class => PlanPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Definir gate para administradores
        Gate::define('admin-only', function ($user) {
            return $user && $user->role === 'admin';
        });
    }
}
