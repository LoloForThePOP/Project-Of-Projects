<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email","username"}, message="Il y a déjà un utilisateur qui utilise ce choix")
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
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    
    /**
     * @ORM\Column(type="string", length=30, unique=true)
     */
    private $username;

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





    public function __construct()
    {

        $this->parameters['isVerified'] = false;
        $this->PPBases = new ArrayCollection();
        $this->createdPresentations = new ArrayCollection();
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
        return (string) $this->email;
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

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
}
