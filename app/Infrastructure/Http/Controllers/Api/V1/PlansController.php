<?php

namespace App\Infrastructure\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Http\Resources\PlanResource;
use App\Infrastructure\Persistence\Eloquent\Plans\PlanModel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class PlansController extends Controller
{
    use AuthorizesRequests;

    /**
     * Lista todos los planes activos
     */
    public function index(): JsonResponse
    {
        // Los planes son públicos, no requiere autorización
        $plans = PlanModel::where('is_active', true)->get();
        
        return response()->json([
            'data' => PlanResource::collection($plans),
            'meta' => [
                'total' => $plans->count()
            ]
        ]);
    }

    /**
     * Crea un nuevo plan
     */
    public function store(StorePlanRequest $request): JsonResponse
    {
        // Verificar autorización usando política
        $this->authorize('create', PlanModel::class);

        $plan = PlanModel::create([
            'name' => $request->name,
            'monthly_price' => $request->monthly_price,
            'user_limit' => $request->user_limit,
            'features' => $request->features,
            'is_active' => true
        ]);

        return response()->json([
            'data' => new PlanResource($plan),
            'message' => 'Plan created successfully'
        ], 201);
    }

    /**
     * Muestra un plan específico
     */
    public function show(int $id): JsonResponse
    {
        // Los planes son públicos, no requiere autorización
        $plan = PlanModel::findOrFail($id);
        
        return response()->json([
            'data' => new PlanResource($plan)
        ]);
    }

    /**
     * Actualiza un plan existente
     */
    public function update(UpdatePlanRequest $request, int $id): JsonResponse
    {
        $plan = PlanModel::findOrFail($id);
        
        // Verificar autorización usando política
        $this->authorize('update', $plan);

        $plan->update($request->only([
            'name', 'monthly_price', 'user_limit', 'features', 'is_active'
        ]));

        return response()->json([
            'data' => new PlanResource($plan->fresh()),
            'message' => 'Plan updated successfully'
        ]);
    }

    /**
     * Elimina (desactiva) un plan
     */
    public function destroy(int $id): JsonResponse
    {
        $plan = PlanModel::findOrFail($id);
        
        // Verificar autorización usando política
        $this->authorize('delete', $plan);
        
        // Soft delete: marcar como inactivo en lugar de eliminar
        $plan->update(['is_active' => false]);

        return response()->json([
            'message' => 'Plan deactivated successfully'
        ]);
    }
}