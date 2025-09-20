<?php

use App\Infrastructure\Http\Controllers\Api\V1\AuthController;
use App\Infrastructure\Http\Controllers\Api\V1\PlansController;
use App\Infrastructure\Http\Controllers\Api\V1\TenantsController;
use App\Infrastructure\Http\Controllers\Api\V1\TenantSubscriptionsController;
use App\Infrastructure\Http\Controllers\Api\V1\TenantUsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Rutas de autenticación
    Route::post('login', [AuthController::class, 'login']);
    
    // Rutas públicas de planes (solo lectura)
    Route::get('plans', [PlansController::class, 'index']);
    Route::get('plans/{plan}', [PlansController::class, 'show']);
    
    // Rutas protegidas que requieren autenticación
    Route::middleware('auth:sanctum')->group(function () {
        // Gestión de planes (crear, actualizar, eliminar)
        Route::post('plans', [PlansController::class, 'store']);
        Route::put('plans/{plan}', [PlansController::class, 'update']);
        Route::patch('plans/{plan}', [PlansController::class, 'update']);
        Route::delete('plans/{plan}', [PlansController::class, 'destroy']);
        
        // Rutas para Tenants/Empresas (CRUD completo)
        Route::apiResource('tenants', TenantsController::class);
        
        // Rutas para usuarios de empresas (CRUD completo con validación de límites)
        Route::apiResource('tenants.users', TenantUsersController::class);
        
        // Rutas para suscripciones de tenants
        Route::post('tenants/{tenantId}/subscriptions/change-plan', [TenantSubscriptionsController::class, 'changePlan']);
    });

    // Fallback para rutas no encontradas bajo /api/v1 -> siempre JSON
    Route::fallback(function () {
        return response()->json([
            'message' => 'Not Found',
        ], 404);
    });
});
