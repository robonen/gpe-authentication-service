<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\Length(min: 1, max: 255)]
    #[Assert\Email]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[Assert\Length(min: 4, max: 12)]
    #[ORM\Column(length: 25, nullable: true)]
    private ?string $phone = null;

    #[Assert\Length(min: 6, max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password = null;

    // TODO: Roles validator
    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $roles = [];

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $email_verified_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt()
    {

    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getEmailVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->email_verified_at;
    }

    public function setEmailVerifiedAt(): self
    {
        if ($this->email_verified_at == null) {
            $this->email_verified_at = new \DateTimeImmutable('now');
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(): self
    {
        if ($this->created_at == null) {
            $this->created_at = new \DateTimeImmutable('now');
        }

        return $this;
    }

    public function getUserIdentifier(): string
    {
        // TODO: Implement getUserIdentifier() method
    }
}
