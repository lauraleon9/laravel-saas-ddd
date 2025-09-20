<?php

namespace App\Infrastructure\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Infrastructure\Persistence\Eloquent\Tenants\TenantModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class TenantUsersController extends Controller
{
    public function index(int $tenantId): JsonResponse
    {
        $users = User::where('tenant_id', $tenantId)
                    ->where('is_active', true)
                    ->get();
        
        return response()->json(['data' => $users]);
    }

    public function store(Request $request, int $tenantId): JsonResponse
    {
        // Validar que el tenant existe
        $tenant = TenantModel::findOrFail($tenantId);
        
        // Validar datos
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,NULL,id,tenant_id,' . $tenantId,
            'password' => 'required|string|min:8',
            'role' => 'sometimes|in:admin,user'
        ]);

        // Verificar límite de usuarios del plan activo
        $activeSubscription = $tenant->activeSubscription;
        if ($activeSubscription) {
            $currentUserCount = User::where('tenant_id', $tenantId)
                                  ->where('is_active', true)
                                  ->count();
            
            if ($currentUserCount >= $activeSubscription->user_limit_snapshot) {
                return response()->json([
                    'message' => 'User limit reached for current plan',
                    'current_users' => $currentUserCount,
                    'user_limit' => $activeSubscription->user_limit_snapshot
                ], 422);
            }
        }

        $user = User::create([
            'tenant_id' => $tenantId,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'user',
            'is_active' => true
        ]);

        return response()->json([
            'data' => $user,
            'message' => 'User created successfully'
        ], 201);
    }

    public function show(int $tenantId, int $userId): JsonResponse
    {
        $user = User::where('tenant_id', $tenantId)->findOrFail($userId);
        return response()->json(['data' => $user]);
    }

    public function update(Request $request, int $tenantId, int $userId): JsonResponse
    {
        $user = User::where('tenant_id', $tenantId)->findOrFail($userId);
        
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $userId . ',id,tenant_id,' . $tenantId,
            'role' => 'sometimes|in:admin,user',
            'is_active' => 'sometimes|boolean'
        ]);

        $user->update($request->only(['name', 'email', 'role', 'is_active']));

        return response()->json([
            'data' => $user->fresh(),
            'message' => 'User updated successfully'
        ]);
    }

    public function destroy(int $tenantId, int $userId): JsonResponse
    {
        $user = User::where('tenant_id', $tenantId)->findOrFail($userId);
        $user->update(['is_active' => false]);

        return response()->json(['message' => 'User deactivated successfully']);
    }
}