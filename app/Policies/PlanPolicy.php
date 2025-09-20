<?php

namespace App\Policies;

use App\Infrastructure\Persistence\Eloquent\Plans\PlanModel;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PlanPolicy
{
    /**
     * Determine whether the user can view any models.
     * Todos los usuarios pueden ver los planes disponibles
     */
    public function viewAny(?User $user): bool
    {
        return true; // Los planes son públicos para consulta
    }

    /**
     * Determine whether the user can view the model.
     * Todos pueden ver un plan específico
     */
    public function view(?User $user, PlanModel $planModel): bool
    {
        return true; // Los planes son públicos
    }

    /**
     * Determine whether the user can create models.
     * Solo administradores pueden crear planes
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     * Solo administradores pueden actualizar planes
     */
    public function update(User $user, PlanModel $planModel): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     * Solo administradores pueden eliminar planes
     */
    public function delete(User $user, PlanModel $planModel): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PlanModel $planModel): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PlanModel $planModel): bool
    {
        return $user->role === 'admin';
    }
}
