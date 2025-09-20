<?php

namespace App\Infrastructure\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Tenants\TenantModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TenantsController extends Controller
{
    public function index(): JsonResponse
    {
        $tenants = TenantModel::with('activeSubscription.plan')->get();
        return response()->json(['data' => $tenants]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Hacer slug opcional; si no viene, se genera desde name y se garantiza unicidad
            'slug' => 'sometimes|nullable|string|max:255|unique:tenants,slug',
            'status' => 'sometimes|in:active,inactive,suspended',
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['name']);
        $slug = $this->ensureUniqueSlug($slug);

        $tenant = TenantModel::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'status' => $validated['status'] ?? 'active',
        ]);

        return response()->json(['data' => $tenant, 'message' => 'Tenant created successfully'], 201);
    }

    public function show(int $id): JsonResponse
    {
        $tenant = TenantModel::with('activeSubscription.plan')->findOrFail($id);
        return response()->json(['data' => $tenant]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $tenant = TenantModel::findOrFail($id);
        
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:tenants,slug,' . $id,
            'status' => 'sometimes|in:active,inactive,suspended'
        ]);

        $tenant->update($request->only(['name', 'slug', 'status']));

        return response()->json(['data' => $tenant->fresh(), 'message' => 'Tenant updated successfully']);
    }

    public function destroy(int $id): JsonResponse
    {
        $tenant = TenantModel::findOrFail($id);
        $tenant->update(['status' => 'inactive']);

        return response()->json(['message' => 'Tenant deactivated successfully']);
    }

    /**
     * Garantiza que el slug sea único agregando un sufijo incremental si es necesario.
     */
    private function ensureUniqueSlug(string $slug): string
    {
        $base = $slug;
        $i = 1;
        while (TenantModel::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }
        return $slug;
    }
}