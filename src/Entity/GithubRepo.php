<?php

namespace App\Entity;

use App\Repository\GithubRepoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GithubRepoRepository::class)]
#[ORM\Index(columns: ['repository_id'], name: 'repository_id_idx')]
class GithubRepo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $repository_id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $html_url = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $pushed_at = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $stargazers_count = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getRepositoryId(): ?int
    {
        return $this->repository_id;
    }

    public function setRepositoryId(int $repository_id): static
    {
        $this->repository_id = $repository_id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getHtmlUrl(): ?string
    {
        return $this->html_url;
    }

    public function setHtmlUrl(string $html_url): static
    {
        $this->html_url = $html_url;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getPushedAt(): ?\DateTimeInterface
    {
        return $this->pushed_at;
    }

    public function setPushedAt(\DateTimeInterface $pushed_at): static
    {
        $this->pushed_at = $pushed_at;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStargazersCount(): ?int
    {
        return $this->stargazers_count;
    }

    public function setStargazersCount(int $stargazers_count): static
    {
        $this->stargazers_count = $stargazers_count;

        return $this;
    }
}
