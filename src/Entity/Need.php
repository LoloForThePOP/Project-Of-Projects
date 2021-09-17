<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\NeedRepository;

use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=NeedRepository::class)
 */
class Need
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    const TYPES = ["skill", "task", "material", "advice", "area", "money", "other"];

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Choice(choices=Need::TYPES, message="Veuillez renseigner un type de besoin valide")
     */
    private $type;

    const ISPAID = ["yes", "maybe", "no"];
    
    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\Choice(choices=Need::ISPAID, message="Veuillez renseigner un type de transaction valide (payé ou non ou peut-être)")
     */
    private $isPaid;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\NotBlank 
     * @Assert\Length(
     *      min = 5,
     *      max = 100,
     *      minMessage = "Le Titre du Besoin doit faire au minimum {{ limit }} caractères",
     *      maxMessage = "Le Titre du Besoin doit faire au maximum {{ limit }} caractères",
     * )
     * 
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=PPBase::class, inversedBy="needs")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $presentation;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $position;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;




    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getPresentation(): ?PPBase
    {
        return $this->presentation;
    }

    public function setPresentation(?PPBase $presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getIsPaid(): ?string
    {
        return $this->isPaid;
    }

    public function setIsPaid(?string $isPaid): self
    {
        $this->isPaid = $isPaid;

        return $this;
    }
}
