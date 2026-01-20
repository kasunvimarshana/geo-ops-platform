<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\SubscriptionLimit;

class SubscriptionService
{
    public function getCurrentSubscription(int $organizationId): array
    {
        $organization = Organization::with(['subscriptionLimit'])->find($organizationId);

        if (!$organization) {
            throw new \Exception('Organization not found');
        }

        $limit = $organization->subscriptionLimit;

        return [
            'package' => $organization->subscription_package,
            'expires_at' => $organization->subscription_expires_at,
            'status' => $organization->status,
            'is_active' => $organization->subscription_expires_at > now(),
            'days_remaining' => now()->diffInDays($organization->subscription_expires_at, false),
            'limits' => $limit ? [
                'max_users' => $limit->max_users,
                'max_machines' => $limit->max_machines,
                'max_lands_per_month' => $limit->max_lands_per_month,
                'max_storage_mb' => $limit->max_storage_mb,
                'can_export_pdf' => $limit->can_export_pdf,
                'can_generate_reports' => $limit->can_generate_reports,
            ] : null,
        ];
    }

    public function checkFeature(int $organizationId, string $feature): bool
    {
        $organization = Organization::with(['subscriptionLimit'])->find($organizationId);

        if (!$organization || !$organization->subscriptionLimit) {
            return false;
        }

        $limit = $organization->subscriptionLimit;

        return match($feature) {
            'export_pdf' => $limit->can_export_pdf,
            'generate_reports' => $limit->can_generate_reports,
            default => false,
        };
    }

    public function checkLimit(int $organizationId, string $limitType): array
    {
        $organization = Organization::with(['subscriptionLimit'])->find($organizationId);

        if (!$organization || !$organization->subscriptionLimit) {
            return [
                'allowed' => false,
                'reason' => 'No subscription limit found',
            ];
        }

        $limit = $organization->subscriptionLimit;

        return match($limitType) {
            'users' => [
                'allowed' => $organization->users()->count() < $limit->max_users,
                'current' => $organization->users()->count(),
                'max' => $limit->max_users,
            ],
            'machines' => [
                'allowed' => $organization->machines()->count() < $limit->max_machines,
                'current' => $organization->machines()->count(),
                'max' => $limit->max_machines,
            ],
            'lands' => [
                'allowed' => $organization->lands()
                    ->whereMonth('created_at', now()->month)
                    ->count() < $limit->max_lands_per_month,
                'current' => $organization->lands()
                    ->whereMonth('created_at', now()->month)
                    ->count(),
                'max' => $limit->max_lands_per_month,
            ],
            default => [
                'allowed' => false,
                'reason' => 'Unknown limit type',
            ],
        };
    }
}
