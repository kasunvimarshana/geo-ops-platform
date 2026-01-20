<?php

namespace App\DTOs\Auth;

/**
 * Data Transfer Object for user registration.
 *
 * @property int $organization_id
 * @property string $role
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property string $password
 */
class RegisterDTO
{
    /**
     * Create a new RegisterDTO instance.
     */
    public function __construct(
        public readonly int $organization_id,
        public readonly string $role,
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly string $password,
    ) {}

    /**
     * Create a DTO from an array of data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            organization_id: $data['organization_id'],
            role: $data['role'] ?? 'owner',
            first_name: $data['first_name'],
            last_name: $data['last_name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            password: $data['password'],
        );
    }

    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        return [
            'organization_id' => $this->organization_id,
            'role' => $this->role,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => $this->password,
            'is_active' => true,
        ];
    }
}
