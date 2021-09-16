<?php

namespace App\Entity;

use DateTime;
use Serializable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PPBaseRepository;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;

use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;


/**
 * @ORM\Entity(repositoryClass=PPBaseRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class PPBase implements \Serializable
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
     *     mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/webp"},
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
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $creator;


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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Slide::class, mappedBy="presentation")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $slides;

    const MAX_SLIDES = 8;

    /**
     * @ORM\OneToMany(targetEntity=Place::class, mappedBy="presentation")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $places;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $otherComponents = [];

    /**
     * @ORM\OneToMany(targetEntity=Document::class, mappedBy="presentation")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity=Need::class, mappedBy="presentation")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $needs;

    /**
     * @ORM\OneToMany(targetEntity=Conversation::class, mappedBy="presentation")
     */
    private $conversations;

    /**
     * @ORM\OneToMany(targetEntity=ContributorStructure::class, mappedBy="presentation")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $contributorStructures;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $cache = [];




    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->isAdminValidated = true;
        $this->overallQualityAssessment = 3;
        $this->parameters['arePrivateMessagesActivated'] = true;
        $this->cache['thumbnail'] = null;
        $this->isPublished = true;
        $this->isDeleted = false;
        $this->viewsCount = 0;

        // unique stringId is generated through $this->generateStringId() called in LifecycleCallbacks() PrePersist
        $this->categories = new ArrayCollection();
        $this->slides = new ArrayCollection();
        $this->places = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->needs = new ArrayCollection();
        $this->conversations = new ArrayCollection();
        $this->contributorStructures = new ArrayCollection();
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
        if ($this->otherComponents !== null && array_key_exists($key, $this->otherComponents)) {
            return $this->otherComponents[$key];
        }

        return [];
    }

    /**
     * 
     */
    public function getOCItem($component_type, $item_id)
    {
        if ($this->otherComponents !== null && array_key_exists($component_type, $this->otherComponents)) {

            foreach ($this->otherComponents[$component_type] as &$item) {

                if ($item['id']==$item_id) {
                    return $item;
                }

            }

            return null;

        }

        return null;
    }

    
    /**
     * 
     */
    public function setOCItem($component_type, $item_id, $updatedItem)
    {
        if ($this->otherComponents !== null && array_key_exists($component_type, $this->otherComponents)) {

            foreach ($this->otherComponents[$component_type] as &$item) {

                if ($item['id']==$item_id) {
                    
                    $item = $updatedItem;
                    return true;
                }


            }

            return null;

        }

        return null;
    }

    public function addOtherComponentItem($component_type, $item)
    {
        if ($component_type!==null) {

            $item['id'] = uniqid();
            $item['position'] = count($this->getOC($component_type));
            $this->otherComponents[$component_type][] = $item;

            return $this;
        }
    }

    public function deleteOtherComponentItem($component_type, $id)
    {

        $i=0;
        foreach($this->otherComponents[$component_type] as $element) {
            //check the property of every element
            if($element['id']==$id){
                unset($this->otherComponents[$component_type][$i]);
            }
            $i++;
        }

        $this->otherComponents[$component_type] = array_values($this->otherComponents[$component_type]);
        
        return $this;
    }


    public function positionOtherComponentItem($component_type, $itemsPositions)
    {
        if ($component_type!==null) {

            foreach ($this->otherComponents[$component_type] as &$item) {

                $newPosition = array_search($item['id'], $itemsPositions, false);

                $item['position']=$newPosition;

            }

            //reordering items by position

            usort($this->otherComponents[$component_type], function ($item1, $item2) {
                return $item1['position'] <=> $item2['position'];
            });

            //dump($this->otherComponents[$component_type]);

            return $this;
        }

    }

    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setPresentation($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getPresentation() === $this) {
                $document->setPresentation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Need[]
     */
    public function getNeeds(): Collection
    {
        return $this->needs;
    }

    public function addNeed(Need $need): self
    {
        if (!$this->needs->contains($need)) {
            $this->needs[] = $need;
            $need->setPresentation($this);
        }

        return $this;
    }

    public function removeNeed(Need $need): self
    {
        if ($this->needs->removeElement($need)) {
            // set the owning side to null (unless already changed)
            if ($need->getPresentation() === $this) {
                $need->setPresentation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Conversation[]
     */
    public function getConversations(): Collection
    {
        return $this->conversations;
    }

    public function addConversation(Conversation $conversation): self
    {
        if (!$this->conversations->contains($conversation)) {
            $this->conversations[] = $conversation;
            $conversation->setPresentation($this);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): self
    {
        if ($this->conversations->removeElement($conversation)) {
            // set the owning side to null (unless already changed)
            if ($conversation->getPresentation() === $this) {
                $conversation->setPresentation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ContributorStructure[]
     */
    public function getContributorStructures(): Collection
    {
        return $this->contributorStructures;
    }

    /**
     * @return array
     */
    public function getContributorStructuresByType(string $type): array
    {
        $contributorStructures = $this->contributorStructures;

        $typedContributorStructures = [];

        foreach ($contributorStructures as $cs) {

            if ($cs->getType() == $type) {

                $typedContributorStructures[] = $cs;
            }

        }

        return $typedContributorStructures;
    }

    public function addContributorStructure(ContributorStructure $contributorStructure): self
    {
        if (!$this->contributorStructures->contains($contributorStructure)) {
            $this->contributorStructures[] = $contributorStructure;
            $contributorStructure->setPresentation($this);
        }

        return $this;
    }

    public function removeContributorStructure(ContributorStructure $contributorStructure): self
    {
        if ($this->contributorStructures->removeElement($contributorStructure)) {
            // set the owning side to null (unless already changed)
            if ($contributorStructure->getPresentation() === $this) {
                $contributorStructure->setPresentation(null);
            }
        }

        return $this;
    }

    public function getCache(): ?array
    {
        return $this->cache;
    }

    public function setCache(?array $cache): self
    {
        $this->cache = $cache;

        return $this;
    }
    
    public function getCacheItem($key)
    {
        return $this->cache[$key];
    }

    public function setCacheItem($key, $value): self
    {
        $this->cache[$key] = $value;

        return $this;
    }





}
