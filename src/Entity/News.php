<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Repository\NewsRepository;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;


use Symfony\Component\HttpFoundation\File\File;

use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;


/**
 * Represents a project news
 * 
 * @ORM\Entity(repositoryClass=NewsRepository::class)
 * 
 * @ORM\HasLifecycleCallbacks
 * 
 * @Vich\Uploadable
 */
class News
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
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $textContent;

    /**
     * @ORM\ManyToOne(targetEntity=PPBase::class, inversedBy="news")
     */
    private $project;


    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="news")
     */
    private $author;


         
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image1;
        
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image2;
        
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image3;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $captionImage1;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $captionImage2;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $captionImage3;










    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     *  @Assert\Image(
     *     maxSize = "6500k",
     *     maxSizeMessage = "Poids maximal Accepté pour l'image : {{ limit }} {{ suffix }}",
     *     mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/gif", "image/apng", "image/webp"},
     *     mimeTypesMessage = "Le format de fichier ({{ type }}) n'est pas encore pris en compte. Les formats acceptés sont : {{ types }}"
     * )
     * @Vich\UploadableField(mapping="news_image", fileNameProperty="image1")
     * 
     * @var File|null
     */
    public $image1File;

    
    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $image1File
     */
    public function setImage1File(?File $image1File = null): void
    {
        $this->image1File = $image1File;

        //if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        //}
    }




    public function getImage1File(): ?File
    {
        return $this->image1File;
    }

    
    public function getImage1(): ?string
    {
        return $this->image1;
    }

    public function setImage1(?string $image1): self
    {
        $this->image1 = $image1;

        return $this;
    }


    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     *  @Assert\Image(
     *     maxSize = "6500k",
     *     maxSizeMessage = "Poids maximal Accepté pour l'image : {{ limit }} {{ suffix }}",
     *     mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/gif", "image/apng", "image/webp"},
     *     mimeTypesMessage = "Le format de fichier ({{ type }}) n'est pas encore pris en compte. Les formats acceptés sont : {{ types }}"
     * )
     * @Vich\UploadableField(mapping="news_image", fileNameProperty="image2")
     * 
     * @var File|null
     */
    public $image2File;

    
    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $image2File
     */
    public function setImage2File(?File $image2File = null): void
    {
        $this->image2File = $image2File;

        //if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        //}
    }

    public function getImage2File(): ?File
    {
        return $this->image2File;
    }

    
    public function getImage2(): ?string
    {
        return $this->image2;
    }

    public function setImage2(?string $image2): self
    {
        $this->image2 = $image2;

        return $this;
    }





    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     *  @Assert\Image(
     *     maxSize = "6500k",
     *     maxSizeMessage = "Poids maximal Accepté pour l'image : {{ limit }} {{ suffix }}",
     *     mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/gif", "image/apng", "image/webp"},
     *     mimeTypesMessage = "Le format de fichier ({{ type }}) n'est pas encore pris en compte. Les formats acceptés sont : {{ types }}"
     * )
     * @Vich\UploadableField(mapping="news_image", fileNameProperty="image3")
     * 
     * @var File|null
     */
    public $image3File;

    
    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $image3File
     */
    public function setImage3File(?File $image3File = null): void
    {
        $this->image3File = $image3File;

        //if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        //}
    }

    public function getImage3File(): ?File
    {
        return $this->image3File;
    }

    
    public function getImage3(): ?string
    {
        return $this->image3;
    }

    public function setImage3(?string $image3): self
    {
        $this->image3 = $image3;

        return $this;
    }




    public function __construct()
    {

        $this->createdAt = new \DateTimeImmutable();

    }

    
   /**
    * Allow to Automaticaly setUpdatedAt
    * 
    * @ORM\PreUpdate
    */
    public function updatedTimestamp(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable());
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getTextContent(): ?string
    {
        return $this->textContent;
    }

    public function setTextContent(?string $textContent): self
    {
        $this->textContent = $textContent;

        return $this;
    }

    public function getProject(): ?PPBase
    {
        return $this->project;
    }

    public function setProject(?PPBase $project): self
    {
        $this->project = $project;

        return $this;
    }


    public function getCaptionImage1(): ?string
    {
        return $this->captionImage1;
    }

    public function setCaptionImage1(?string $captionImage1): self
    {
        $this->captionImage1 = $captionImage1;

        return $this;
    }

    public function getCaptionImage2(): ?string
    {
        return $this->captionImage2;
    }

    public function setCaptionImage2(?string $captionImage2): self
    {
        $this->captionImage2 = $captionImage2;

        return $this;
    }

    public function getCaptionImage3(): ?string
    {
        return $this->captionImage3;
    }

    public function setCaptionImage3(?string $captionImage3): self
    {
        $this->captionImage3 = $captionImage3;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }


















}


