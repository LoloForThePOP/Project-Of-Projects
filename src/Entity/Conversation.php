<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConversationRepository::class)
 */
class Conversation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=PPBase::class, inversedBy="conversations")
    */ 
    private $presentation;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="conversation")
     */
    private $messages;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="conversations")
     */
    private $users;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $cache = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $context;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="createdConversations")
     */
    private $authorUser;





    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->createdAt = new \DateTime('now');
        $this->users = new ArrayCollection();

        
        $this->setCacheItem("lastMessAuthorId", null);
        $this->setCacheItem("lastMessAuthorName", null);
        $this->setCacheItem("lastMessExtract", null);
        $this->setCacheItem("lastMessIsConsulted", null);
        $this->setCacheItem("lastMessDate", null);

    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPresentation(): ?PPBase
    {
        return $this->presentation;
    }

    public function setPresentation(?PPBase $presentation): self
    {
        $this->presentation = $presentation;

        if ($presentation != null) {
            $this->setContext(substr($presentation->getGoal(), 0 , 120));
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {

            $this->messages[] = $message;
            $message->setConversation($this);

            $this->setCacheItem("lastMessAuthorId", $message->getAuthorUser()->getId());
            $this->setCacheItem("lastMessAuthorName", $message->getAuthorUser()->getUserName());
            $this->setCacheItem("lastMessIsConsulted", false);
            $this->setCacheItem("lastMessExtract", substr($message->getContent(), 0, 25));
            $this->setCacheItem("lastMessDate", $message->getCreatedAt());

        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }

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

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

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

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(?string $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getAuthorUser(): ?User
    {
        return $this->authorUser;
    }

    public function setAuthorUser(?User $authorUser): self
    {
        $this->authorUser = $authorUser;

        return $this;
    }







}
