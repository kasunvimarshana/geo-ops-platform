<?php

namespace App\DTOs\FieldJob;

use Illuminate\Http\Request;

/**
 * Update Job DTO
 * 
 * Data Transfer Object for updating a field job.
 */
class UpdateJobDTO
{
    public function __construct(
        public readonly ?int $landId = null,
        public readonly ?int $customerId = null,
        public readonly ?string $serviceType = null,
        public readonly ?string $customerName = null,
        public readonly ?string $customerPhone = null,
        public readonly ?string $customerAddress = null,
        public readonly ?array $locationCoordinates = null,
        public readonly ?float $areaAcres = null,
        public readonly ?float $areaHectares = null,
        public readonly ?float $ratePerUnit = null,
        public readonly ?string $rateUnit = null,
        public readonly ?float $estimatedAmount = null,
        public readonly ?float $actualAmount = null,
        public readonly ?string $scheduledDate = null,
        public readonly ?string $notes = null,
        public readonly ?string $status = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            landId: $request->input('land_id'),
            customerId: $request->input('customer_id'),
            serviceType: $request->input('service_type'),
            customerName: $request->input('customer_name'),
            customerPhone: $request->input('customer_phone'),
            customerAddress: $request->input('customer_address'),
            locationCoordinates: $request->input('location_coordinates'),
            areaAcres: $request->input('area_acres'),
            areaHectares: $request->input('area_hectares'),
            ratePerUnit: $request->input('rate_per_unit'),
            rateUnit: $request->input('rate_unit'),
            estimatedAmount: $request->input('estimated_amount'),
            actualAmount: $request->input('actual_amount'),
            scheduledDate: $request->input('scheduled_date'),
            notes: $request->input('notes'),
            status: $request->input('status'),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            landId: $data['land_id'] ?? null,
            customerId: $data['customer_id'] ?? null,
            serviceType: $data['service_type'] ?? null,
            customerName: $data['customer_name'] ?? null,
            customerPhone: $data['customer_phone'] ?? null,
            customerAddress: $data['customer_address'] ?? null,
            locationCoordinates: $data['location_coordinates'] ?? null,
            areaAcres: $data['area_acres'] ?? null,
            areaHectares: $data['area_hectares'] ?? null,
            ratePerUnit: $data['rate_per_unit'] ?? null,
            rateUnit: $data['rate_unit'] ?? null,
            estimatedAmount: $data['estimated_amount'] ?? null,
            actualAmount: $data['actual_amount'] ?? null,
            scheduledDate: $data['scheduled_date'] ?? null,
            notes: $data['notes'] ?? null,
            status: $data['status'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'land_id' => $this->landId,
            'customer_id' => $this->customerId,
            'service_type' => $this->serviceType,
            'customer_name' => $this->customerName,
            'customer_phone' => $this->customerPhone,
            'customer_address' => $this->customerAddress,
            'location_coordinates' => $this->locationCoordinates,
            'area_acres' => $this->areaAcres,
            'area_hectares' => $this->areaHectares,
            'rate_per_unit' => $this->ratePerUnit,
            'rate_unit' => $this->rateUnit,
            'estimated_amount' => $this->estimatedAmount,
            'actual_amount' => $this->actualAmount,
            'scheduled_date' => $this->scheduledDate,
            'notes' => $this->notes,
            'status' => $this->status,
        ], fn($value) => $value !== null);
    }
}
