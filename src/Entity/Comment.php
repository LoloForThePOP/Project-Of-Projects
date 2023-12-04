<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Exception;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

   /**
     * @ORM\Column(type="text")
     * @Assert\Length(
     *      min = 2,
     *      max = 2500,
     *      minMessage = "Le commentaire doit contenir au minimum {{ limit }} caractères",
     *      maxMessage = "Le commentaire doit contenir au plus {{ limit }} caractères"
     *      )
     *
     */
    private $content;

    /**
     * @ORM\Column(type="boolean")
     */
    private $approved;

    /**
     * @ORM\ManyToOne(targetEntity=PPBase::class, inversedBy="comments")
     */
    private $projectPresentation;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Comment::class, inversedBy="replies")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="parent", cascade={"persist", "remove"})
     */
    private $replies;

    /**
     * We can even reply to a child comment (child from first degree). We store the user we reply to (so that we can show  it on frontend)
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $repliedUser;

    /**
     * @ORM\ManyToOne(targetEntity=News::class, inversedBy="comments")
     */
    private $news;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="comments")
     */
    private $article;


    public function __construct()    {


    $this->createdAt =  new \DateTimeImmutable('now');
    $this->replies = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $Content): self
    {
        $this->content = $Content;

        return $this;
    }

    public function getApproved(): ?bool
    {
        return $this->approved;
    }

    public function setApproved(bool $approved): self
    {
        $this->approved = $approved;

        return $this;
    }

    public function getProjectPresentation(): ?PPBase
    {
        return $this->projectPresentation;
    }

    public function setProjectPresentation(?PPBase $projectPresentation): self
    {
        $this->projectPresentation = $projectPresentation;

        return $this;
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

     /**
    * Allow to Automaticaly setUpdatedAt
    * 
    * @ORM\PreUpdate
    */
    public function updatedTimestamp(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable('now'));
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

    

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getReplies(): Collection
    {
        return $this->replies;
    }

    public function addReply(self $reply): self
    {
        if (!$this->replies->contains($reply)) {
            $this->replies[] = $reply;
            $reply->setParent($this);
        }

        return $this;
    }

    public function removeReply(self $reply): self
    {
        if ($this->replies->removeElement($reply)) {
            // set the owning side to null (unless already changed)
            if ($reply->getParent() === $this) {
                $reply->setParent(null);
            }
        }

        return $this;
    }


    /**
     * Check if this comment has been created by the commented entity author (so we can highlight its name) (ex: we highlight an article creator's name when he answer to one comment about his article). (note : one day, if we have team that own an entity, we could highligth comments created by a team staff member, so team is used in the method name).
     *
     * @return boolean
    */
    public function isCreatedByEntityTeam($entityName): bool
    {
        switch ($entityName) {

            case 'projectPresentation':

                if ($this->getProjectPresentation()->getCreator() == $this->getUser()) {

                    return true;
        
                }

                break;

            case 'article':

                if ($this->getArticle()->getAuthor() == $this->getUser()) {

                    return true;
        
                }

                break;
            
            default:
                break;
        }

        return false;
        
    }

    /**
     * We can comment an news, an article, etc. This function allows us to know which entity is commented.
     *
     * @return string
    */
    public function getCommentedEntityType()
    {

        if ($this->getProjectPresentation() !== null) {
            return "projectPresentation";
        }

        if ($this->getArticle() !== null) {
            return "article";
        }

        if ($this->getNews() !== null) {
            return "news";
        }

        throw new Exception("Unknow commented entity type");

    }



    public function getRepliedUser(): ?User
    {
        return $this->repliedUser;
    }

    public function setRepliedUser(?User $repliedUser): self
    {
        $this->repliedUser = $repliedUser;

        return $this;
    }

    public function getNews(): ?News
    {
        return $this->news;
    }

    public function setNews(?News $news): self
    {
        $this->news = $news;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    
}
