<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\SubscriptionPackage;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use Illuminate\Support\Collection;

class SubscriptionService
{
    public function __construct(
        protected SubscriptionRepositoryInterface $subscriptionRepository
    ) {}

    public function getAllPackages(): Collection
    {
        return $this->subscriptionRepository->getAllActive();
    }

    public function getCurrentSubscription(Organization $organization): array
    {
        $package = $this->subscriptionRepository->findByName($organization->package_tier);
        $usage = $this->subscriptionRepository->getOrganizationUsage($organization->id);

        $packageLimits = $package ? [
            'lands' => $package->max_lands,
            'measurements' => $package->max_measurements,
            'jobs' => $package->max_jobs,
            'drivers' => $package->max_drivers,
            'storage_mb' => $package->max_storage_mb,
        ] : [];

        $usagePercentages = [];
        $warnings = [];

        if ($package) {
            foreach (['lands', 'measurements', 'jobs', 'drivers'] as $resource) {
                $limit = $packageLimits[$resource];
                $current = $usage[$resource];
                
                if ($limit > 0) {
                    $percentage = round(($current / $limit) * 100, 2);
                    $usagePercentages[$resource] = $percentage;

                    if ($percentage >= 100) {
                        $warnings[] = "You have reached the limit for {$resource} ({$current}/{$limit})";
                    } elseif ($percentage >= 80) {
                        $warnings[] = "You are approaching the limit for {$resource} ({$current}/{$limit}, {$percentage}%)";
                    }
                } else {
                    $usagePercentages[$resource] = 0;
                }
            }
        }

        return [
            'package_tier' => $organization->package_tier,
            'package_expires_at' => $organization->package_expires_at?->toISOString(),
            'is_expired' => $organization->hasExpiredPackage(),
            'package_limits' => $packageLimits,
            'current_usage' => $usage,
            'usage_percentages' => $usagePercentages,
            'warnings' => $warnings,
        ];
    }

    public function checkLimit(Organization $organization, string $resource, ?int $count = null): array
    {
        $package = $this->subscriptionRepository->findByName($organization->package_tier);
        
        if (!$package) {
            return [
                'can_perform' => false,
                'reason' => 'No active subscription package found',
                'current_usage' => 0,
                'limit' => 0,
                'available' => 0,
            ];
        }

        $usage = $this->subscriptionRepository->getOrganizationUsage($organization->id);
        $currentUsage = $usage[$resource] ?? 0;
        
        $limit = match($resource) {
            'lands' => $package->max_lands,
            'measurements' => $package->max_measurements,
            'jobs' => $package->max_jobs,
            'drivers' => $package->max_drivers,
            default => 0,
        };

        $requestCount = $count ?? 1;
        $available = max(0, $limit - $currentUsage);
        $canPerform = ($currentUsage + $requestCount) <= $limit;

        return [
            'can_perform' => $canPerform,
            'reason' => $canPerform 
                ? "You can add {$requestCount} more {$resource}" 
                : "Adding {$requestCount} {$resource} would exceed your package limit of {$limit}",
            'current_usage' => $currentUsage,
            'limit' => $limit,
            'available' => $available,
            'requested' => $requestCount,
        ];
    }

    public function calculateUsagePercentage(int $current, int $limit): float
    {
        if ($limit <= 0) {
            return 0.0;
        }
        return round(($current / $limit) * 100, 2);
    }
}
