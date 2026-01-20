<?php

namespace App\Services;

use App\Models\SyncLog;
use App\Repositories\Interfaces\LandRepositoryInterface;
use App\Repositories\Interfaces\JobRepositoryInterface;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Repositories\Interfaces\ExpenseRepositoryInterface;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SyncService
{
    public function __construct(
        private LandRepositoryInterface $landRepository,
        private JobRepositoryInterface $jobRepository,
        private InvoiceRepositoryInterface $invoiceRepository,
        private ExpenseRepositoryInterface $expenseRepository,
        private PaymentRepositoryInterface $paymentRepository
    ) {}

    public function bulkSync(array $data, int $organizationId, int $userId): array
    {
        $results = [
            'synced' => 0,
            'conflicts' => 0,
            'errors' => 0,
            'details' => [],
        ];

        DB::beginTransaction();
        
        try {
            foreach ($data['items'] as $item) {
                try {
                    $result = $this->syncItem($item, $organizationId, $userId);
                    
                    if ($result['status'] === 'synced') {
                        $results['synced']++;
                    } elseif ($result['status'] === 'conflict') {
                        $results['conflicts']++;
                    }
                    
                    $results['details'][] = $result;
                    
                } catch (\Exception $e) {
                    $results['errors']++;
                    $results['details'][] = [
                        'entity_type' => $item['entity_type'],
                        'offline_id' => $item['offline_id'],
                        'status' => 'error',
                        'message' => $e->getMessage(),
                    ];
                }
            }

            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $results;
    }

    private function syncItem(array $item, int $organizationId, int $userId): array
    {
        $repository = $this->getRepository($item['entity_type']);
        
        $existing = $repository->findByOfflineId($item['offline_id'], $organizationId);

        if ($existing) {
            if ($existing->updated_at > $item['updated_at']) {
                $this->logConflict($item, $organizationId, $userId, $existing);
                
                return [
                    'entity_type' => $item['entity_type'],
                    'offline_id' => $item['offline_id'],
                    'status' => 'conflict',
                    'server_data' => $existing,
                    'client_data' => $item['data'],
                ];
            }

            $repository->update($existing->id, array_merge($item['data'], [
                'sync_status' => 'synced',
            ]));

            $this->logSync($item, $organizationId, $userId, $existing->id, 'updated');

            return [
                'entity_type' => $item['entity_type'],
                'offline_id' => $item['offline_id'],
                'entity_id' => $existing->id,
                'status' => 'synced',
                'action' => 'updated',
            ];
        }

        $entity = $repository->create(array_merge($item['data'], [
            'organization_id' => $organizationId,
            'offline_id' => $item['offline_id'],
            'sync_status' => 'synced',
        ]));

        $this->logSync($item, $organizationId, $userId, $entity->id, 'created');

        return [
            'entity_type' => $item['entity_type'],
            'offline_id' => $item['offline_id'],
            'entity_id' => $entity->id,
            'status' => 'synced',
            'action' => 'created',
        ];
    }

    public function resolveConflict(int $syncLogId, string $resolution, int $organizationId): array
    {
        $syncLog = SyncLog::where('id', $syncLogId)
            ->where('organization_id', $organizationId)
            ->first();

        if (!$syncLog) {
            throw new \Exception('Sync log not found');
        }

        if ($syncLog->sync_status !== 'conflict') {
            throw new \Exception('Not a conflict');
        }

        DB::beginTransaction();
        
        try {
            $repository = $this->getRepository($syncLog->entity_type);
            
            if ($resolution === 'use_server') {
                $syncLog->update([
                    'sync_status' => 'resolved',
                    'synced_at' => now(),
                ]);
            } elseif ($resolution === 'use_client') {
                $clientData = $syncLog->conflict_data['client_data'] ?? [];
                
                $repository->update($syncLog->entity_id, array_merge($clientData, [
                    'sync_status' => 'synced',
                ]));

                $syncLog->update([
                    'sync_status' => 'resolved',
                    'synced_at' => now(),
                ]);
            }

            DB::commit();
            
            return [
                'status' => 'resolved',
                'resolution' => $resolution,
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getSyncStatus(int $organizationId): array
    {
        return [
            'lands' => [
                'pending' => count($this->landRepository->getPendingSync($organizationId)),
            ],
            'jobs' => [
                'pending' => count($this->jobRepository->getPendingSync($organizationId)),
            ],
            'invoices' => [
                'pending' => count($this->invoiceRepository->getPendingSync($organizationId)),
            ],
            'expenses' => [
                'pending' => count($this->expenseRepository->getPendingSync($organizationId)),
            ],
            'payments' => [
                'pending' => count($this->paymentRepository->getPendingSync($organizationId)),
            ],
            'conflicts' => SyncLog::where('organization_id', $organizationId)
                ->where('sync_status', 'conflict')
                ->count(),
        ];
    }

    private function getRepository(string $entityType)
    {
        return match($entityType) {
            'land' => $this->landRepository,
            'job' => $this->jobRepository,
            'invoice' => $this->invoiceRepository,
            'expense' => $this->expenseRepository,
            'payment' => $this->paymentRepository,
            default => throw new \Exception('Unknown entity type'),
        };
    }

    private function logSync(array $item, int $organizationId, int $userId, int $entityId, string $action): void
    {
        SyncLog::create([
            'organization_id' => $organizationId,
            'user_id' => $userId,
            'entity_type' => $item['entity_type'],
            'entity_id' => $entityId,
            'offline_id' => $item['offline_id'],
            'action' => $action,
            'sync_status' => 'synced',
            'synced_at' => now(),
        ]);
    }

    private function logConflict(array $item, int $organizationId, int $userId, $existing): void
    {
        SyncLog::create([
            'organization_id' => $organizationId,
            'user_id' => $userId,
            'entity_type' => $item['entity_type'],
            'entity_id' => $existing->id,
            'offline_id' => $item['offline_id'],
            'action' => 'sync',
            'sync_status' => 'conflict',
            'conflict_data' => [
                'server_data' => $existing->toArray(),
                'client_data' => $item['data'],
                'server_updated_at' => $existing->updated_at,
                'client_updated_at' => $item['updated_at'],
            ],
        ]);
    }
}
