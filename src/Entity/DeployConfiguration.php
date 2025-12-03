<?php

namespace App\Entity;

use App\Repository\DeployConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeployConfigurationRepository::class)]
class DeployConfiguration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 250)]
    private ?string $repositoryOwner = null;

    #[ORM\Column(length: 100)]
    private ?string $repositoryName = null;

    #[ORM\Column(length: 255)]
    private ?string $deployPath = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRepositoryOwner(): ?string
    {
        return $this->repositoryOwner;
    }

    public function setRepositoryOwner(string $repositoryOwner): static
    {
        $this->repositoryOwner = $repositoryOwner;

        return $this;
    }

    public function getRepositoryName(): ?string
    {
        return $this->repositoryName;
    }

    public function setRepositoryName(string $repositoryName): static
    {
        $this->repositoryName = $repositoryName;

        return $this;
    }

    public function getDeployPath(): ?string
    {
        return $this->deployPath;
    }

    public function setDeployPath(string $deployPath): static
    {
        $this->deployPath = $deployPath;

        return $this;
    }
}
