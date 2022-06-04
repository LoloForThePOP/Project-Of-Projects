<?php

namespace App\Entity;

use App\Repository\PlaceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlaceRepository::class)
 */
class Place
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $latitude;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $longitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $administrativeAreaLevel1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $administrativeAreaLevel2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $locality;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sublocalityLevel1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $postalCode;

    /**
     * @ORM\ManyToOne(targetEntity=PPBase::class, inversedBy="places")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $presentation;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $position;

    /**
     * @ORM\Column(type="json")
     * 
     * note : variable notation convention is broken here due to Algolia Search Engine constraint.
     * 
     */
    private $_geoloc = [];



    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getAdministrativeAreaLevel1(): ?string
    {
        return $this->administrativeAreaLevel1;
    }

    public function setAdministrativeAreaLevel1(?string $administrativeAreaLevel1): self
    {
        $this->administrativeAreaLevel1 = $administrativeAreaLevel1;

        return $this;
    }

    public function getAdministrativeAreaLevel2(): ?string
    {
        return $this->administrativeAreaLevel2;
    }

    public function setAdministrativeAreaLevel2(?string $administrativeAreaLevel2): self
    {
        $this->administrativeAreaLevel2 = $administrativeAreaLevel2;

        return $this;
    }

    public function getLocality(): ?string
    {
        return $this->locality;
    }

    public function setLocality(?string $locality): self
    {
        $this->locality = $locality;

        return $this;
    }

    public function getSublocalityLevel1(): ?string
    {
        return $this->sublocalityLevel1;
    }

    public function setSublocalityLevel1(?string $sublocalityLevel1): self
    {
        $this->sublocalityLevel1 = $sublocalityLevel1;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getPresentation(): ?PPBase
    {
        return $this->presentation;
    }

    public function setPresentation(?PPBase $presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getGeoloc(): ?array
    {
        return $this->_geoloc;
    }

    public function setGeoloc(array $_geoloc): self
    {
        $this->_geoloc = $_geoloc;

        return $this;
    }
}
