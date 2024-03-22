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
     * @ORM\Column(type="string", length=50)
     */
    private $type;

    const STATUS = ["PENDING", "PAID"];

    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $buyerEmail;


    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="purchases")
     */
    private $registredUser;

    /**
     * 
     * Potential other user info like telephone, country, adress
     * 
     * @ORM\Column(type="json", nullable=true)
     */
    private $buyerInfo = [];

    /**
     * 
     * Other purchase info. For example, total purchase amount. If we consider a donation, witch project (id) received the donation.
     * 
     * @ORM\Column(type="json")
     */
    private $content = [];



    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices=Purchase::STATUS, message="Veuillez renseigner un statut de transaction valide (ex: PENDING; PAID")
     */
    private $status;


    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;


       

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


    public function getContentItem($key)
    {
        return $this->content[$key];
    }

    public function setContentItem($key, $value): self
    {
        $this->content[$key] = $value;

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

    public function getBuyerEmail(): ?string
    {
        return $this->buyerEmail;
    }

    public function setBuyerEmail(string $buyerEmail): self
    {
        $this->buyerEmail = $buyerEmail;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
