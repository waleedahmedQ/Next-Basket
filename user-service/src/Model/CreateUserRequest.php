<?php

namespace App\Model;

class CreateUserRequest
{
    /**
     * @Assert\NotBlank(message="Email is required")
     * @Assert\Email(message="Invalid email address")
     */
    private string $email;

    /**
     * @Assert\NotBlank(message="First name is required")
     * @Assert\Length(min=2, minMessage="First name must be at least {{ limit }} characters long")
     */
    private string $firstName;

    /**
     * @Assert\NotBlank(message="Last name is required")
     * @Assert\Length(min=2, minMessage="Last name must be at least {{ limit }} characters long")
     */
    private string $lastName;

    // Getters and setters
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }
}
