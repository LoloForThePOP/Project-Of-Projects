<?php

namespace App\Entity;

use Serializable;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\ArticleRepository;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints\Image;

use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;


/**
 * Represents a Propon Article
 * 
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 * 
 */
class Article implements Serializable
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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articles")
     */
    private $author;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isValidated;

    /**
     * the name of the thumbnail image file (example : thumb-4234564567.jpg)
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $thumbnail;


    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $data = [];



    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     *  @Assert\Image(
     *     maxSize = "5500k",
     *     maxSizeMessage = "Poids maximal accepté pour l'image : {{ limit }} {{ suffix }}",
     *     mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/webp"},
     *     mimeTypesMessage = "Le format de fichier ({{ type }}) n'est pas encore pris en compte. Les formats acceptés sont : {{ types }}"
     * )
     * @Vich\UploadableField(mapping="article_thumbnail", fileNameProperty="thumbnail")
     * 
     * @var File|null
     */
    public $thumbnailFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }


    /**
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $thumbnailFile
     */
    public function setThumbnailFile(?File $thumbnailFile = null): void
    {
        $this->thumbnailFile = $thumbnailFile;

        // It is required that at least one field changes if you are using doctrine
        // otherwise the event listeners won't be called and the file is lost
        // So we update one field

        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getThumbnailFile(): ?File
    {
        return $this->thumbnailFile;
    }
















    

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('now');
        $this->isValidated = true;


        $this->data['viewsCount'] = 0;
        $this->data['short_editorial_text_fr'] = null;
    }


    public function serialize()
    {
        return serialize($this->id);
    }
    
    public function unserialize($serialized)
    {
    $this->id = unserialize($serialized);
    
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

    /**
    * Allow to Automaticaly set updatedAt
    * 
    * @ORM\PreUpdate
    */
    public function updatedTimestamp(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable());
    }


    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getIsValidated(): ?bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): self
    {
        $this->isValidated = $isValidated;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getDataItem($key)
    {
        return $this->data[$key];
    }

    public function setDataItem($key, $value): self
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function unsetDataItem($key)
    {
        unset($this->data[$key]);

        return true;
    }



}
