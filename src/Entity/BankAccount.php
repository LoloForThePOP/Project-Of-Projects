<?php

namespace App\Entity;

use App\Repository\BankAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BankAccountRepository::class)
 */
class BankAccount
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $SurnameName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $IBAN;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $BIC;

    /**
     * @ORM\OneToMany(targetEntity=PPBase::class, mappedBy="bankAccount")
     */
    private $presentation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    public function __construct()
    {
        $this->presentation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSurnameName(): ?string
    {
        return $this->SurnameName;
    }

    public function setSurnameName(string $SurnameName): self
    {
        $this->SurnameName = $SurnameName;

        return $this;
    }

    public function getIBAN(): ?string
    {
        return $this->IBAN;
    }

    public function setIBAN(string $IBAN): self
    {
        $this->IBAN = $IBAN;

        return $this;
    }

    public function getBIC(): ?string
    {
        return $this->BIC;
    }

    public function setBIC(string $BIC): self
    {
        $this->BIC = $BIC;

        return $this;
    }

    /**
     * @return Collection<int, PPBase>
     */
    public function getPresentation(): Collection
    {
        return $this->presentation;
    }

    public function addPresentation(PPBase $presentation): self
    {
        if (!$this->presentation->contains($presentation)) {
            $this->presentation[] = $presentation;
            $presentation->setBankAccount($this);
        }

        return $this;
    }

    public function removePresentation(PPBase $presentation): self
    {
        if ($this->presentation->removeElement($presentation)) {
            // set the owning side to null (unless already changed)
            if ($presentation->getBankAccount() === $this) {
                $presentation->setBankAccount(null);
            }
        }

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
}
