<?php

namespace App\DTOs;

class CustomerDTO
{
    public string $id;
    public string $name;
    public string $email;
    public string $phone;
    public string $address;
    public string $organizationId;

    public function __construct(string $id, string $name, string $email, string $phone, string $address, string $organizationId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
        $this->organizationId = $organizationId;
    }
}