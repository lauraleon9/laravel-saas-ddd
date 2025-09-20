<?php
namespace App\Application\Subscriptions\UseCases;

use App\Domain\Subscriptions\Repositories\SubscriptionRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Plans\PlanModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ChangeTenantPlan
{
    private SubscriptionRepositoryInterface $subs;

    public function __construct(SubscriptionRepositoryInterface $subs)
    {
        $this->subs = $subs;
    }

    public function __invoke(int $tenantId, int $planId): array
    {
        $plan = PlanModel::whereKey($planId)->where('is_active', 1)->first();
        if (!$plan) {
            throw new ModelNotFoundException('Plan not found or inactive');
        }

        $snap = [
            'price'      => (float) $plan->monthly_price,
            'user_limit' => $plan->user_limit,
            'features'   => $plan->features ?? null,
        ];

        return DB::transaction(function () use ($tenantId, $planId, $snap) {
            $this->subs->closeActiveIfAny($tenantId);
            return $this->subs->openNew($tenantId, $planId, $snap);
        });
    }
}
