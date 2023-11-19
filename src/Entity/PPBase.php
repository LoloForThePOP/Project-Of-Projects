<?php

namespace App\Entity;

use DateTime;
use Serializable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PPBaseRepository;
use App\Entity\ProjectStatus;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;

use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


/**
 * @ORM\Entity(repositoryClass=PPBaseRepository::class)
 * @UniqueEntity(fields={"stringId"}, message="Un utilisateur utilise déjà le nom qui a été choisit. Essayer avec un autre nom.")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class PPBase implements \Serializable, NormalizableInterface
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
     * the name of the logo image file (example : logo-4234564567.jpg)
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;


    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     *  @Assert\Image(
     *     maxSize = "5000k",
     *     maxSizeMessage = "Le poids maximal accepté pour chaque logo est de {{ limit }} {{ suffix }}",
     *     mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/webp"},
     *     mimeTypesMessage = "Pour ajouter un logo, le format de fichier ({{ type }}) n'est pas encore pris en compte. Les formats acceptés sont : {{ types }}"
     * )
     * @Vich\UploadableField(mapping="project_logo_image", fileNameProperty="logo")
     * 
     * @var File|null
     */
    public $logoFile;

    /**
     * the name of the custom thumbnail image file (example : custom-thumb-4234564567.jpg)
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $customThumbnail;


    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     *  @Assert\Image(
     *     maxSize = "5500k",
     *     maxSizeMessage = "Poids maximal accepté pour l'image : {{ limit }} {{ suffix }}",
     *     mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/webp"},
     *     mimeTypesMessage = "Pour ajouter votr evignette, le format de fichier ({{ type }}) n'est pas encore pris en compte. Les formats acceptés sont : {{ types }}"
     * )
     * @Vich\UploadableField(mapping="project_custom_thumbnail_image", fileNameProperty="customThumbnail")
     * 
     * @var File|null
     */
    public $customThumbnailFile;

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
     * Used in url as a unique presentation page identifier
     * 
     * @ORM\Column(type="string", length=191, unique=true)
     * 
     * @Assert\Length(
     *      min = 1,
     *      max = 191,
     *      minMessage = "Le nom doit contenir au moins {{ limit }} caractères",
     *      maxMessage = "Le nom doit contenir au plus {{ limit }} caractères"
     *      )
     * 
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
     * @ORM\OneToMany(targetEntity=Place::class, mappedBy="presentation", cascade={"persist", "remove"})
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

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $data = [];

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="projectPresentation")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=Like::class, mappedBy="projectPresentation")
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity=News::class, mappedBy="project")
     */
    private $news;

    /**
     * @ORM\OneToMany(targetEntity=Follow::class, mappedBy="project")
     */
    private $follows;




    public function __construct()
    {
        // note : string identifier (stringId property) is generated in LifecycleCallbacks PrePersist (so "like a constructor call") through $this->generateStringId() (see method below).

        $this->createdAt = new DateTime();

        $this->isAdminValidated = false;
        $this->overallQualityAssessment = 0;
        $this->isPublished = true;
        $this->isDeleted = false;

        $this->parameters['arePrivateMessagesActivated'] = true;

        $this->data['validatedStringId'] = false; // flag to know wether presentation slug is still a randomized string (false) or user has validated his own slug (ex: propon.org/my-project instead of propon.org/tr3H2Y).

        $this->data['remove-helper-invite'] = false; // flag to know wether displaying an helper invite to user is usefull anymore.

        $this->data['viewsCount'] = 0;

        $this->data['short_editorial_text_fr'] = null;

        $this->cache['thumbnailParentImageAddress'] = null;
        $this->cache['thumbnailAddress'] = null;

        $this->categories = new ArrayCollection();
        $this->slides = new ArrayCollection();
        $this->places = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->needs = new ArrayCollection();
        $this->conversations = new ArrayCollection();
        $this->contributorStructures = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->news = new ArrayCollection();
        $this->follows = new ArrayCollection();
    }

    public function __toString()
    {

        return $this->getGoal();
        
    }







    public function serialize()
    {
        return serialize($this->id);
    }
    
    public function unserialize($serialized)
    {
        $this->id = unserialize($serialized);
    }


    /**
     * Data sent to Algolia
    */
    public function normalize(NormalizerInterface $serializer, $format = null, array $context = []): array
    {
        return [
            'id' => $this->getId(),
            'goal' => $this->getGoal(),
            'keywords' => $this->getKeywords(),
            'title' => $this->getTitle(),
            'stringId' => $this->getStringId(),
            'cache' => $this->getCache(),
            'stringId' => $this->getStringId(),
            '_geoloc' => $this->getGeoLocations(),
            'categories' => $this->getCategoriesAlgolia(),
            
        ];
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

    /**
     * @return string|null
     */
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
    

    public function getCustomThumbnail(): ?string
    {
        return $this->customThumbnail;
    }

    public function setCustomThumbnail(?string $customThumbnail): self
    {
        $this->customThumbnail = $customThumbnail;

        return $this;
    }


    /**
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $customThumbnailFile
     */
    public function setCustomthumbnailFile(?File $customThumbnailFile = null): void
    {
        $this->customThumbnailFile = $customThumbnailFile;

        // It is required that at least one field changes if you are using doctrine
        // otherwise the event listeners won't be called and the file is lost
        // So we update one field

        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getCustomThumbnailFile(): ?File
    {
        return $this->customThumbnailFile;
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

    public function isSearchable(){

        if ($this->isPublished == false) {
            return false;
        }

        if ($this->isDeleted != false) {
            return false;
        }

        if ($this->isAdminValidated != true) {
            return false;
        }

        if ($this->overallQualityAssessment < 2) {
            return false;
        }

        return true;

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


    public function setStringId(?string $stringId): self
    {
        $this->stringId = $stringId;

        return $this;
    }


    /**
     * @Groups({"searchable"})
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

    
    /*
    * used to provide categories in algolia index
    */
    public function getCategoriesAlgolia(): ?array
    {

        $categories=$this->getCategories();

        $dataForAlgolia=[];

        if (!$categories->isEmpty()) {

            foreach ($categories as $category) {

                $dataForAlgolia[]=[

                    'uniqueName' => $category->getUniqueName(),
                    'descriptionEn' => $category->getDescriptionEn(),
                    'descriptionFr' => $category->getDescriptionFr(),

                ];
            }
    
            return $dataForAlgolia;
        }

        else return null;
       
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
    * Allow to Automaticaly set updatedAt
    * 
    * @ORM\PreUpdate
    */
    public function updatedTimestamp(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable());
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
     * 
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

    /*
    * used to provide geoloc at the root of each record in algolia index (as needed)
    */
    public function getGeolocations(): ?array
    {


        $places=$this->getPlaces();      

        if (!$places->isEmpty()) {

            foreach ($places as $place) {
                $geoLocs=$place->getGeoloc();
            }
    
            return $geoLocs;
        }

        else return null;
       
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
                    $item['updatedAt'] = new \DateTimeImmutable();
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
            $item['createdAt'] = new \DateTimeImmutable();
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


    public function getStatusCatalog()
    {

        return ProjectStatus::CATALOG;


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


    public function editProjectStatus($statusType, $value){

        if (isset($this->otherComponents['status'][$statusType])) {

            $editValues = 
                [
                    'value' => $value,
                    'updatedAt' => new \DateTimeImmutable(),
                ];

            $this->otherComponents['status'][$statusType] = array_merge($this->otherComponents['status'][$statusType], $editValues);

        }

        else {
           
            $this->otherComponents['status'][$statusType] = [
                'value' => $value,
                'createdAt' => new \DateTimeImmutable(),
            ];
        }

    }

    public function getProjectStatus($statusType = null){

        $output = null;

        if ($statusType !== null) {
            $output = $this->otherComponents['status'][$statusType];
        } else {
            $output = $this->otherComponents['status'];
        }

        return $output;

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
    
    public function unsetCacheItem($key)
    {
        unset($this->cache[$key]);
        
        return true;
    }

    public function setCacheItem($key, $value): self
    {
        $this->cache[$key] = $value;

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

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setProjectPresentation($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getProjectPresentation() === $this) {
                $comment->setProjectPresentation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setProjectPresentation($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getProjectPresentation() === $this) {
                $like->setProjectPresentation(null);
            }
        }

        return $this;
    }

    /**
     * 
     * @param User $user
     * @return boolean
     */
    public function isLikedByUser(User $user): bool
    {
        
        foreach($this->likes as $like){

            if($like->getUser() === $user){
                return true;
            }

        }

        return false;
    }



    /**
     * 
     * @param User $user
     * @return boolean
     */
    public function isFollowedByUser(User $user): bool
    {
        
        foreach($this->follows as $follow){

            if($follow->getUser() === $user){
                return true;
            }

        }

        return false;
    }
    

    /**
     * @return Collection<int, News>
     */
    public function getNews(): Collection
    {
        return $this->news;
    }

    public function addNews(News $news): self
    {
        if (!$this->news->contains($news)) {
            $this->news[] = $news;
            $news->setProject($this);
        }

        return $this;
    }

    public function removeNews(News $news): self
    {
        if ($this->news->removeElement($news)) {
            // set the owning side to null (unless already changed)
            if ($news->getProject() === $this) {
                $news->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Follow>
     */
    public function getFollows(): Collection
    {
        return $this->follows;
    }

    /**
     * Return an array of User Objects following a presentation
     */
    public function getFollowers()
    {

        $followingObjects = $this->getFollows();
        $followers = [];

        foreach ($followingObjects as $followingObject) {
            $followers[] = $followingObject->getUser();
        }

        return $followers;
    }

    public function addFollow(Follow $follow): self
    {
        if (!$this->follows->contains($follow)) {
            $this->follows[] = $follow;
            $follow->setProject($this);
        }

        return $this;
    }

    public function removeFollow(Follow $follow): self
    {
        if ($this->follows->removeElement($follow)) {
            // set the owning side to null (unless already changed)
            if ($follow->getProject() === $this) {
                $follow->setProject(null);
            }
        }

        return $this;
    }

    




}
