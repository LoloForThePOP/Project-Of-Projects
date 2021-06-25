<?php

namespace App\Entity;

use App\Repository\PersorgRepository;
use Doctrine\ORM\Mapping as ORM;


use Symfony\Component\HttpFoundation\File\File;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Image;


use Vich\UploaderBundle\Mapping\Annotation as Vich;

use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;


/**
 * 
 * Represents a Person or an organisation (Pers - Org)
 * 
 * @ORM\Entity(repositoryClass=PersorgRepository::class)
 * @Vich\Uploadable
 * 
 */
class Persorg
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $missions;

    
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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $website1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $website2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $website3;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $postalMail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tel1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tel2;

    
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;


    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     *  @Assert\Image(
     *     maxSize = "1500k",
     *     maxSizeMessage = "Poids maximal Accepté pour l'image : 1500 k",
     *     mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/gif"},
     *     mimeTypesMessage = "Le format de fichier ({{ type }}) n'est pas encore pris en compte. Les formats acceptés sont : {{ types }}"
     * )
     * @Vich\UploadableField(mapping="persorg_image", fileNameProperty="image")
     * 
     * @var File|null
     */
    public $imageFile;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $website4;

    /**
     * @ORM\ManyToOne(targetEntity=ContributorStructure::class, inversedBy="persorgs")
     */
    private $contributorStructure;

















    
    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
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


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMissions(): ?string
    {
        return $this->missions;
    }

    public function setMissions(?string $missions): self
    {
        $this->missions = $missions;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getWebsite1(): ?string
    {
        return $this->website1;
    }

    public function setWebsite1(?string $website1): self
    {
        $this->website1 = $website1;

        return $this;
    }

    public function getWebsite2(): ?string
    {
        return $this->website2;
    }

    public function setWebsite2(?string $website2): self
    {
        $this->website2 = $website2;

        return $this;
    }

    public function getWebsite3(): ?string
    {
        return $this->website3;
    }

    public function setWebsite3(?string $website3): self
    {
        $this->website3 = $website3;

        return $this;
    }

    public function getPostalMail(): ?string
    {
        return $this->postalMail;
    }

    public function setPostalMail(?string $postalMail): self
    {
        $this->postalMail = $postalMail;

        return $this;
    }

    public function getTel1(): ?string
    {
        return $this->tel1;
    }

    public function setTel1(?string $tel1): self
    {
        $this->tel1 = $tel1;

        return $this;
    }

    public function getTel2(): ?string
    {
        return $this->tel2;
    }

    public function setTel2(?string $tel2): self
    {
        $this->tel2 = $tel2;

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

    public function getWebsite4(): ?string
    {
        return $this->website4;
    }

    public function setWebsite4(?string $website4): self
    {
        $this->website4 = $website4;

        return $this;
    }

    public function getContributorStructure(): ?ContributorStructure
    {
        return $this->contributorStructure;
    }

    public function setContributorStructure(?ContributorStructure $contributorStructure): self
    {
        $this->contributorStructure = $contributorStructure;

        return $this;
    }

}
