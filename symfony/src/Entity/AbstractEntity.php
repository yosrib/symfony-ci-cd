<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Serializer\Annotation\Groups;

class AbstractEntity
{
    #[Groups(groups: ['get'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id;

    #[Timestampable(on: 'create')]
    #[Groups(groups: ['get'])]
    #[ORM\Column(type: 'datetime', nullable: false)]
    protected ?\DateTimeInterface $createdDate;

    #[Timestampable(on: 'update')]
    #[Groups(groups: ['get'])]
    #[ORM\Column(type: 'datetime', nullable: false)]
    protected ?\DateTimeInterface $updateDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(?\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->updateDate;
    }

    public function setUpdateDate(?\DateTimeInterface $updateDate): self
    {
        $this->updateDate = $updateDate;

        return $this;
    }
}
