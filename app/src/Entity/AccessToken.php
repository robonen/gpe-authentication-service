<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AccessTokenRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AccessTokenRepository::class)]
class AccessToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length:  255)]
    #[Groups('main')]
    private ?string $token = null;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"main"})
     */
    private ?\DateTimeImmutable $activeTill;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getActiveTill(): ?\DateTimeImmutable
    {
        return $this->activeTill;
    }

    public function setActiveTill(\DateTimeImmutable $activeTill): self
    {
        $this->activeTill = $activeTill;

        return $this;
    }
}
