<?php
namespace App\Domain\Subscriptions\Repositories;

interface SubscriptionRepositoryInterface {
    public function getActiveByTenant(int $tenantId): ?array;
    public function closeActiveIfAny(int $tenantId): void;
    public function openNew(int $tenantId, int $planId, array $snapshots): array;
}
