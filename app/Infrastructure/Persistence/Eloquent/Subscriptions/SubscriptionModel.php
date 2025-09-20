<?php
namespace App\Infrastructure\Persistence\Eloquent\Subscriptions;

use App\Infrastructure\Persistence\Eloquent\Plans\PlanModel;
use App\Infrastructure\Persistence\Eloquent\Tenants\TenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionModel extends Model {
    protected $table = 'subscriptions';
    protected $fillable = [
        'tenant_id','plan_id','starts_at','ends_at','is_active',
        'price_at_signup','user_limit_snapshot','features_snapshot'
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'features_snapshot' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(TenantModel::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PlanModel::class);
    }
}
