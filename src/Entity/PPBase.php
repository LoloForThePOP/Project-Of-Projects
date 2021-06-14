<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PPBaseRepository;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;


/**
 * @ORM\Entity(repositoryClass=PPBaseRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class PPBase
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=400)
     * 
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 10,
     *      max = 255,
     *      minMessage = "L'objectif doit contenir au moins {{ limit }} caractères",
     *      maxMessage = "L'objectif doit contenir au plus {{ limit }} caractères"
     *      )
     */
    private $goal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;



    /**
     * the name of the image logo file (example : logo-4234564567.jpg)
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;


    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     *  @Assert\Image(
     *     maxSize = "1500k",
     *     maxSizeMessage = "Poids maximal Accepté pour l'image : 1500 k",
     *     mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/gif"},
     *     mimeTypesMessage = "Le format de fichier ({{ type }}) n'est pas encore pris en compte. Les formats acceptés sont : {{ types }}"
     * )
     * @Vich\UploadableField(mapping="project_logo_image", fileNameProperty="logo")
     * 
     * @var File|null
     */
    public $logoFile;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $keywords;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $textDescription;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAdminValidated;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $overallQualityAssessment;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDeleted;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $viewsCount;

    /**
     * Contains some parameters about this presentation
     * 
     * here is a list : arePrivateMessagesActivated
     * 
     * @ORM\Column(type="json", nullable=true)
     */
    private $parameters = [];

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="createdPresentations")
     */
    private $creator;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $thumbnail;

    /**
     * Used in urls as an unique short identifier
     * 
     * @ORM\Column(type="string", length=10)
     */
    private $stringId;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, mappedBy="projects")
     */
    private $categories;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Slide::class, mappedBy="presentation")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $slides;

    /**
     * @ORM\OneToMany(targetEntity=Place::class, mappedBy="presentation")
     */
    private $places;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $otherComponents = [];



    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->isAdminValidated = true;
        $this->parameters['arePrivateMessagesActivated'] = true;
        $this->isPublished = true;
        $this->isDeleted = false;
        $this->viewsCount = 0;

        // unique stringId is generated through $this->generateStringId() called in LifecycleCallbacks() PrePersist
        $this->categories = new ArrayCollection();
        $this->slides = new ArrayCollection();
        $this->places = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGoal(): ?string
    {
        return $this->goal;
    }

    public function setGoal(string $goal): self
    {
        $this->goal = $goal;

        return $this;
    }


    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        if (!$logo == null) {
            $this->thumbnail = $logo;
        }

        return $this;
    }


    /**
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $logoFile
     */
    public function setLogoFile(?File $logoFile = null): void
    {
        $this->logoFile = $logoFile;

        // It is required that at least one field changes if you are using doctrine
        // otherwise the event listeners won't be called and the file is lost
        // So we update one field

        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getLogoFile(): ?File
    {
        return $this->logoFile;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getTextDescription(): ?string
    {
        return $this->textDescription;
    }

    public function setTextDescription(?string $textDescription): self
    {
        $this->textDescription = $textDescription;

        return $this;
    }

    public function getIsAdminValidated(): ?bool
    {
        return $this->isAdminValidated;
    }

    public function setIsAdminValidated(bool $isAdminValidated): self
    {
        $this->isAdminValidated = $isAdminValidated;

        return $this;
    }

    public function getOverallQualityAssessment(): ?int
    {
        return $this->overallQualityAssessment;
    }

    public function setOverallQualityAssessment(?int $overallQualityAssessment): self
    {
        $this->overallQualityAssessment = $overallQualityAssessment;

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(?bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

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

    public function getViewsCount(): ?int
    {
        return $this->viewsCount;
    }

    public function setViewsCount(?int $viewsCount): self
    {
        $this->viewsCount = $viewsCount;

        return $this;
    }

    public function getParameters(): ?array
    {
        return $this->parameters;
    }

    public function setParameters(?array $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function getParameter($key)
    {
        return $this->parameters[$key];
    }

    public function setParameter($key, $value): self
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function getStringId(): ?string
    {
        return $this->stringId;
    }

    public function setStringId(string $stringId): self
    {
        $this->stringId = $stringId;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function generateStringId(LifecycleEventArgs $event): self
    {

        $entityManager = $event->getEntityManager();
        $PPBaseRepository = $entityManager->getRepository(get_class($this));

        while (true) {

            $stringId = base_convert(time() - rand(0, 10000), 10, 36);

            // checking if result is unique

            $twin = $PPBaseRepository->findOneBy(['stringId' => $stringId]);

            if ($twin == null) {
                break;
            }
        }

        $this->stringId = $stringId;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addProject($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeProject($this);
        }

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|Slide[]
     */
    public function getSlides(): Collection
    {
        return $this->slides;
    }

    public function addSlide(Slide $slide): self
    {
        if (!$this->slides->contains($slide)) {
            $this->slides[] = $slide;
            $slide->setPresentation($this);
        }

        return $this;
    }

    public function removeSlide(Slide $slide): self
    {
        if ($this->slides->removeElement($slide)) {
            // set the owning side to null (unless already changed)
            if ($slide->getPresentation() === $this) {
                $slide->setPresentation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Place[]
     */
    public function getPlaces(): Collection
    {
        return $this->places;
    }

    public function addPlace(Place $place): self
    {
        if (!$this->places->contains($place)) {
            $this->places[] = $place;
            $place->setPresentation($this);
        }

        return $this;
    }

    public function removePlace(Place $place): self
    {
        if ($this->places->removeElement($place)) {
            // set the owning side to null (unless already changed)
            if ($place->getPresentation() === $this) {
                $place->setPresentation(null);
            }
        }

        return $this;
    }

    public function getOtherComponents(): ?array
    {

        return $this->otherComponents;
    }

    public function setOtherComponents(?array $otherComponents): self
    {
        $this->otherComponents = $otherComponents;

        return $this;
    }


    public function getOC($key)
    {
        if ($key!==null) {
            return $this->otherComponents[$key];
        }
    }

    public function setOC($key, $value)
    {
        if ($key!==null) {

            $this->otherComponents[$key] = $value;

            return $this;
        }
    }




}
