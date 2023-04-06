<?php

namespace App\Entity;

use App\Repository\SiteRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SiteRepository::class)]
class Site extends AbstractEntity
{
    #[Assert\NotBlank(groups: ['set'])]
    #[Assert\Url(groups: ['set'])]
    #[Groups(['get', 'set'])]
    #[OA\Property(type: 'string')]
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $url = null;

    #[Assert\NotBlank(groups: ['set'])]
    #[Groups(['get', 'set'])]
    #[OA\Property(type: 'string')]
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[Groups(['get', 'set'])]
    #[OA\Property(type: 'string')]
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
