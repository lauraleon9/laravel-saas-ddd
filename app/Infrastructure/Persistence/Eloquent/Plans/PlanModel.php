<?php

namespace App\Infrastructure\Persistence\Eloquent\Plans;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanModel extends Model
{
    use HasFactory;

    protected $table = 'plans';

    protected $fillable = [
        'name',
        'monthly_price',
        'user_limit',
        'features',
        'is_active',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'user_limit' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope para obtener solo planes activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Relación con suscripciones
     */
    public function subscriptions()
    {
        return $this->hasMany(\App\Infrastructure\Persistence\Eloquent\Subscriptions\SubscriptionModel::class, 'plan_id');
    }
}