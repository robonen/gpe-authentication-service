<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[UniqueEntity(fields: ['alias'])]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $alias = null;

    #[Assert\Length(min: 1)]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $redirect = null;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: ProjectSettings::class)]
    private Collection $projectSettings;

    public function __construct()
    {
        $this->projectSettings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRedirect(): ?string
    {
        return $this->redirect;
    }

    public function setRedirect(?string $redirect): self
    {
        $this->redirect = $redirect;

        return $this;
    }

    /**
     * @return Collection<int, ProjectSettings>
     */
    public function getProjectSettings(): Collection
    {
        return $this->projectSettings;
    }

    public function addProjectSetting(ProjectSettings $projectSetting): self
    {
        if (!$this->projectSettings->contains($projectSetting)) {
            $this->projectSettings->add($projectSetting);
            $projectSetting->setProject($this);
        }

        return $this;
    }

    public function removeProjectSetting(ProjectSettings $projectSetting): self
    {
        if ($this->projectSettings->removeElement($projectSetting)) {
            // set the owning side to null (unless already changed)
            if ($projectSetting->getProject() === $this) {
                $projectSetting->setProject(null);
            }
        }

        return $this;
    }
}
