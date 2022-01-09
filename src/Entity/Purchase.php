<?php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Choice;

/**
 * @ORM\Entity(repositoryClass=PurchaseRepository::class)
 */
class Purchase
{


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $buyerInfo = [];

    /**
     * @ORM\Column(type="json")
     */
    private $content = [];

    const STATUS = ["PENDING", "PAID"];

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices=Purchase::STATUS, message="Veuillez renseigner un statut de transaction valide (ex: PENDING; PAID")
     */
    private $status;


    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="purchases")
     */
    private $registredUser;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;
       

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('now');
        $this->status = "PENDING";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBuyerInfo(): ?array
    {
        return $this->buyerInfo;
    }

    public function setBuyerInfo(?array $buyerInfo): self
    {
        $this->buyerInfo = $buyerInfo;

        return $this;
    }

    public function getContent(): ?array
    {
        return $this->content;
    }

    public function setContent(array $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getRegistredUser(): ?User
    {
        return $this->registredUser;
    }

    public function setRegistredUser(?User $registredUser): self
    {
        $this->registredUser = $registredUser;

        return $this;
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
}
