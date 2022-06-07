<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Repository\PlaceRepository;

use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @ORM\Entity(repositoryClass=PlaceRepository::class)
 */
class Place implements NormalizableInterface
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
     * note : variable notation convention is broken here due to an Algolia Search Engine Constraint. ps : not relevant because we use a normalizer to change attributes names we want to change. So, to-do : change _geoloc attribute name.
     * 
     */
    private $_geoloc = [];



    




    public function normalize(NormalizerInterface $serializer, $format = null, array $context = []): array
    {
        return [
            '_geoloc' => $this->getGeoloc(),
        ];
    }




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
