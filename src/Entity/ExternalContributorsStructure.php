<?php

namespace App\Entity;

use App\Repository\ExternalContributorsStructureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ExternalContributorsStructureRepository::class)
 */
class ExternalContributorsStructure
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
    private $title;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $position;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $richTextContent;

    /**
     * @ORM\ManyToOne(targetEntity=PPBase::class, inversedBy="ECS")
     */
    private $presentation;

    /**
     * @ORM\OneToMany(targetEntity=Persorg::class, mappedBy="externalContributorsStructure")
     */
    private $persorgs;

    public function __construct()
    {
        $this->persorgs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getRichTextContent(): ?string
    {
        return $this->richTextContent;
    }

    public function setRichTextContent(?string $richTextContent): self
    {
        $this->richTextContent = $richTextContent;

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

    /**
     * @return Collection|Persorg[]
     */
    public function getPersorgs(): Collection
    {
        return $this->persorgs;
    }

    public function addPersorg(Persorg $persorg): self
    {
        if (!$this->persorgs->contains($persorg)) {
            $this->persorgs[] = $persorg;
            $persorg->setExternalContributorsStructure($this);
        }

        return $this;
    }

    public function removePersorg(Persorg $persorg): self
    {
        if ($this->persorgs->removeElement($persorg)) {
            // set the owning side to null (unless already changed)
            if ($persorg->getExternalContributorsStructure() === $this) {
                $persorg->setExternalContributorsStructure(null);
            }
        }

        return $this;
    }
}
