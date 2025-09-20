<?php

namespace App\Infrastructure\Persistence\Eloquent\Tenants;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TenantModel extends Model
{
    use HasFactory;

    protected $table = 'tenants';

    protected $fillable = [
        'name',
        'slug',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Scope para obtener solo tenants activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Relación con suscripciones
     */
    public function subscriptions()
    {
        return $this->hasMany(\App\Infrastructure\Persistence\Eloquent\Subscriptions\SubscriptionModel::class, 'tenant_id');
    }

    /**
     * Obtener la suscripción activa del tenant
     */
    public function activeSubscription()
    {
        return $this->hasOne(\App\Infrastructure\Persistence\Eloquent\Subscriptions\SubscriptionModel::class, 'tenant_id')
                    ->where('is_active', true);
    }

    /**
     * Relación con usuarios
     */
    public function users()
    {
        return $this->hasMany(\App\Models\User::class, 'tenant_id');
    }
}