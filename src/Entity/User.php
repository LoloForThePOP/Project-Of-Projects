<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * 
 * Unique fields (do not factorize fields in a single assert, to get email AND userName unicity)
 *
 * @UniqueEntity(fields={"email"}, message="Un utilisateur utilise déjà ce choix")
 * @UniqueEntity(fields={"userName"}, message="Un utilisateur utilise déjà ce choix")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    
    /**
     * @ORM\Column(type="string", length=30, unique=true)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 3,
     *      max = 30,
     *      minMessage = "Votre nom d'utilisateur doit faire au moins {{ limit }} caractères",
     *      maxMessage = "Votre nom d'utilisateur doit faire au plus {{ limit }} caractères"
     * )
     */
    private $userName;


    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;


    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * 
     * Contains following parameters 
     * 
     * isVerified (user email has been confirmed, true or false)
     * 
     * @ORM\Column(type="json", nullable=true)
     */
    private $parameters = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $emailValidationToken;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resetPasswordToken;

    /**
     * @ORM\OneToMany(targetEntity=PPBase::class, mappedBy="creator")
     */
    private $createdPresentations;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="authorUser")
     */
    private $messages;

    /**
     * @ORM\OneToOne(targetEntity=Persorg::class, cascade={"persist", "remove"})
     */
    private $persorg;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $userNameSlug;

    /**
     * @ORM\ManyToMany(targetEntity=Conversation::class, mappedBy="users")
     */
    private $conversations;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $data = [];


    public function __construct()
    {

        $this->parameters['isVerified'] = false;
        $this->PPBases = new ArrayCollection();
        $this->createdPresentations = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->conversations = new ArrayCollection();

        $this->setDataItem("messagesCount", 0);

    }



    public function getId(): ?int
    {
        return $this->id;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserName(): string
    {
        return (string) $this->userName;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->userName;
    }
    
    public function setUserName(string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getEmailValidationToken(): ?string
    {
        return $this->emailValidationToken;
    }

    public function setEmailValidationToken(?string $emailValidationToken): self
    {
        $this->emailValidationToken = $emailValidationToken;

        return $this;
    }

    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(?string $resetPasswordToken): self
    {
        $this->resetPasswordToken = $resetPasswordToken;

        return $this;
    }

    /**
     * @return Collection|PPBase[]
     */
    public function getCreatedPresentations(): Collection
    {
        return $this->createdPresentations;
    }

    public function addCreatedPresentation(PPBase $createdPresentation): self
    {
        if (!$this->createdPresentations->contains($createdPresentation)) {
            $this->createdPresentations[] = $createdPresentation;
            $createdPresentation->setCreator($this);
        }

        return $this;
    }

    public function removeCreatedPresentation(PPBase $createdPresentation): self
    {
        if ($this->createdPresentations->removeElement($createdPresentation)) {
            // set the owning side to null (unless already changed)
            if ($createdPresentation->getCreator() === $this) {
                $createdPresentation->setCreator(null);
            }
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
            $message->setAuthorUser($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getAuthorUser() === $this) {
                $message->setAuthorUser(null);
            }
        }

        return $this;
    }

    public function getPersorg(): ?Persorg
    {
        return $this->persorg;
    }

    public function setPersorg(?Persorg $persorg): self
    {
        $this->persorg = $persorg;

        return $this;
    }

    public function getUserNameSlug(): ?string
    {
        return $this->userNameSlug;
    }

    public function setUserNameSlug(string $userNameSlug): self
    {
        $this->userNameSlug = $userNameSlug;

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
            $conversation->addUser($this);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): self
    {
        if ($this->conversations->removeElement($conversation)) {
            $conversation->removeUser($this);
        }

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


    


}
