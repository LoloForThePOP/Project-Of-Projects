<?php

namespace App\Entity;

use App\Repository\FollowRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FollowRepository::class)
 */
class Follow
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="follows")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=PPBase::class, inversedBy="follows")
     */
    private $project;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProject(): ?PPBase
    {
        return $this->project;
    }

    public function setProject(?PPBase $project): self
    {
        $this->project = $project;

        return $this;
    }
}
