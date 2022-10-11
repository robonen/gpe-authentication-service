<?php

namespace App\Entity;

use App\Casts\ProjectSettingsMapper;
use App\Repository\ProjectSettingsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectSettingsRepository::class)]
#[UniqueEntity(fields: ['alias'])]
class ProjectSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Ignore]
    #[ORM\ManyToOne(inversedBy: 'projectSettings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $alias = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $value = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function getValue(mixed $default = null): mixed
    {
        $mapper = new ProjectSettingsMapper();
        return $mapper->get($this->alias, $this->value) ?? $default;
    }

    public function setValue(mixed $value): self
    {
        $mapper = new ProjectSettingsMapper();
        $this->value = $mapper->set($this->alias, $value);

        return $this;
    }
}
