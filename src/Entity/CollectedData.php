<?php

namespace App\Entity;

use App\Repository\CollectedDataRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CollectedDataRepository::class)
 */
class CollectedData
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $DataType;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $data = [];



    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('now');
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDataType(): ?string
    {
        return $this->DataType;
    }

    public function setDataType(string $DataType): self
    {
        $this->DataType = $DataType;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
