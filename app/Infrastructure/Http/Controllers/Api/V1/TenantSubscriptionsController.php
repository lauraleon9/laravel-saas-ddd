<?php
namespace App\Infrastructure\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Plans\PlanModel;
use App\Infrastructure\Persistence\Eloquent\Subscriptions\SubscriptionModel;
use App\Infrastructure\Persistence\Eloquent\Tenants\TenantModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TenantSubscriptionsController extends Controller
{
    /**
     * Cambia el plan activo de un tenant: cierra la suscripción activa (ends_at, is_active=false)
     * y crea una nueva suscripción activa con snapshots del plan.
     */
    public function changePlan(int $tenantId, Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|integer|exists:plans,id'
        ]);

        $tenant = TenantModel::findOrFail($tenantId);
        $plan = PlanModel::where('id', $validated['plan_id'])
            ->where('is_active', true)
            ->firstOrFail();

        // Idempotencia: si ya está en el mismo plan activo, no hacer nada
        $current = SubscriptionModel::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderByDesc('starts_at')
            ->first();

        if ($current && (int)$current->plan_id === (int)$plan->id) {
            return response()->json([
                'message' => 'Plan is already active. No changes applied',
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
            ], 200);
        }

        DB::transaction(function () use ($tenant, $plan, $current) {
            // Cerrar suscripción activa si existe
            if ($current) {
                $current->update([
                    'is_active' => false,
                    'ends_at' => now(),
                ]);
            }

            // Crear nueva suscripción activa con snapshots
            SubscriptionModel::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'starts_at' => now(),
                'ends_at' => null,
                'is_active' => true,
                'price_at_signup' => $plan->monthly_price,
                'user_limit_snapshot' => $plan->user_limit,
                'features_snapshot' => $plan->features,
            ]);
        });

        return response()->json([
            'message' => 'Plan changed successfully',
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
        ], 201);
    }
}
