<?php

namespace App\Entity;

use App\Repository\AccessoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccessoryRepository::class)]
class Accessory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $compatibility = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $releaseDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'createdAccessories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creator = null;

    #[ORM\OneToMany(mappedBy: 'accessory', targetEntity: AccessoryCollection::class, orphanRemoval: true)]
    private Collection $accessoryCollections;

    public function __construct()
    {
        $this->accessoryCollections = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?\DateTimeInterface $releaseDate): static
    {
        $this->releaseDate = $releaseDate;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getCompatibility(): ?string
    {
        return $this->compatibility;
    }

    public function setCompatibility(string $compatibility): static
    {
        $this->compatibility = $compatibility;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): static
    {
        $this->creator = $creator;
        return $this;
    }

    public function getAccessoryCollections(): Collection
    {
        return $this->accessoryCollections;
    }

    public function addAccessoryCollection(AccessoryCollection $accessoryCollection): static
    {
        if (!$this->accessoryCollections->contains($accessoryCollection)) {
            $this->accessoryCollections->add($accessoryCollection);
            $accessoryCollection->setAccessory($this);
        }

        return $this;
    }

    public function removeAccessoryCollection(AccessoryCollection $accessoryCollection): static
    {
        if ($this->accessoryCollections->removeElement($accessoryCollection)) {
            // set the owning side to null (unless already changed)
            if ($accessoryCollection->getAccessory() === $this) {
                $accessoryCollection->setAccessory(null);
            }
        }

        return $this;
    }
}
