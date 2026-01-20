<?php

namespace App\DTOs\FieldJob;

use Illuminate\Http\Request;

/**
 * Create Job DTO
 * 
 * Data Transfer Object for creating a new field job.
 */
class CreateJobDTO
{
    public function __construct(
        public readonly ?int $landId,
        public readonly ?int $customerId,
        public readonly string $serviceType,
        public readonly string $customerName,
        public readonly ?string $customerPhone = null,
        public readonly ?string $customerAddress = null,
        public readonly ?array $locationCoordinates = null,
        public readonly ?float $areaAcres = null,
        public readonly ?float $areaHectares = null,
        public readonly ?float $ratePerUnit = null,
        public readonly string $rateUnit = 'acre',
        public readonly ?float $estimatedAmount = null,
        public readonly ?string $scheduledDate = null,
        public readonly ?string $notes = null,
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
            rateUnit: $request->input('rate_unit', 'acre'),
            estimatedAmount: $request->input('estimated_amount'),
            scheduledDate: $request->input('scheduled_date'),
            notes: $request->input('notes'),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            landId: $data['land_id'] ?? null,
            customerId: $data['customer_id'] ?? null,
            serviceType: $data['service_type'],
            customerName: $data['customer_name'],
            customerPhone: $data['customer_phone'] ?? null,
            customerAddress: $data['customer_address'] ?? null,
            locationCoordinates: $data['location_coordinates'] ?? null,
            areaAcres: $data['area_acres'] ?? null,
            areaHectares: $data['area_hectares'] ?? null,
            ratePerUnit: $data['rate_per_unit'] ?? null,
            rateUnit: $data['rate_unit'] ?? 'acre',
            estimatedAmount: $data['estimated_amount'] ?? null,
            scheduledDate: $data['scheduled_date'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
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
            'scheduled_date' => $this->scheduledDate,
            'notes' => $this->notes,
        ];
    }
}
