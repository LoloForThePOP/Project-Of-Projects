<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;


use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Serializer\Annotation\Groups;


use Vich\UploaderBundle\Mapping\Annotation as Vich;

use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;



/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @Vich\Uploadable
 * 
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $uniqueName;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $descriptionEn;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $descriptionFr;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $position;

    /**
     * @ORM\ManyToMany(targetEntity=PPBase::class, inversedBy="categories")
     */
    private $projects;

    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;






    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;


    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     *  @Assert\Image(
     *     maxSize = "20k",
     *     maxSizeMessage = "Poids maximal Accepté pour l'image : 20 k",
     *     mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/gif", "image/svg", "image/svg+xml", },
     *     mimeTypesMessage = "Le format de fichier ({{ type }}) n'est pas encore pris en compte. Les formats acceptés sont : {{ types }}"
     * )
     * @Vich\UploadableField(mapping="category_icon", fileNameProperty="image")
     * 
     * @var File|null
     */
    public $imageFile;




    public function __construct()
    {
        $this->projects = new ArrayCollection();
    }





    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        //if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        //}
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }


    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUniqueName(): ?string
    {
        return $this->uniqueName;
    }

    public function setUniqueName(string $uniqueName): self
    {
        $this->uniqueName = $uniqueName;

        return $this;
    }

    public function getDescriptionEn(): ?string
    {
        return $this->descriptionEn;
    }

    public function setDescriptionEn(string $descriptionEn): self
    {
        $this->descriptionEn = $descriptionEn;

        return $this;
    }

    
    /**
     * @Groups({"searchable"})
     */
    public function getDescriptionFr(): ?string
    {
        return $this->descriptionFr;
    }

    public function setDescriptionFr(?string $descriptionFr): self
    {
        $this->descriptionFr = $descriptionFr;

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

    /**
     * @return Collection|PPBase[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(PPBase $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
        }

        return $this;
    }

    public function removeProject(PPBase $project): self
    {
        $this->projects->removeElement($project);

        return $this;
    }
}
