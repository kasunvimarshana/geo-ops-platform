<?php

namespace App\DTOs\Auth;

/**
 * Data Transfer Object for user login.
 *
 * @property string $email
 * @property string $password
 */
class LoginDTO
{
    /**
     * Create a new LoginDTO instance.
     */
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {}

    /**
     * Create a DTO from an array of data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
        );
    }

    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
