<?php
namespace App\Infrastructure\Persistence\Eloquent\Subscriptions;

use App\Domain\Subscriptions\Repositories\SubscriptionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    public function getActiveByTenant(int $tenantId): ?array {
        $m = SubscriptionModel::where('tenant_id',$tenantId)->where('is_active',1)->first();
        return $m? $m->toArray() : null;
    }

    public function closeActiveIfAny(int $tenantId): void {
        SubscriptionModel::where('tenant_id',$tenantId)->where('is_active',1)
            ->update(['is_active'=>0,'ends_at'=>now()]);
    }

    public function openNew(int $tenantId, int $planId, array $snapshots): array {
        $data = [
            'tenant_id' => $tenantId,
            'plan_id'   => $planId,
            'starts_at' => now(),
            'ends_at'   => null,
            'is_active' => 1,
            'price_at_signup'   => $snapshots['price'],
            'user_limit_snapshot'=> $snapshots['user_limit'],
            'features_snapshot' => $snapshots['features'] ?? null,
        ];
        $m = SubscriptionModel::create($data);
        return $m->toArray();
    }
}
