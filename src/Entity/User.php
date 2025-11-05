<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: GameCollection::class)]
    private Collection $gameCollections;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ConsoleCollection::class)]
    private Collection $consoleCollections;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: AccessoryCollection::class)]
    private Collection $accessoryCollections;

    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: Game::class)]
    private Collection $createdGames;

    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: Console::class)]
    private Collection $createdConsoles;

    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: Accessory::class)]
    private Collection $createdAccessories;

    public function __construct()
    {
        $this->gameCollections = new ArrayCollection();
        $this->consoleCollections = new ArrayCollection();
        $this->accessoryCollections = new ArrayCollection();
        $this->createdGames = new ArrayCollection();
        $this->createdConsoles = new ArrayCollection();
        $this->createdAccessories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    // Collections getters and setters
    public function getGameCollections(): Collection
    {
        return $this->gameCollections;
    }

    public function getConsoleCollections(): Collection
    {
        return $this->consoleCollections;
    }

    public function getAccessoryCollections(): Collection
    {
        return $this->accessoryCollections;
    }

    public function getCreatedGames(): Collection
    {
        return $this->createdGames;
    }

    public function getCreatedConsoles(): Collection
    {
        return $this->createdConsoles;
    }

    public function getCreatedAccessories(): Collection
    {
        return $this->createdAccessories;
    }
}
