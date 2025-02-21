<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['username'], message: "Ce username est déjà utilisé.")]
#[UniqueEntity(fields: ['email'], message: "Cet email est déjà utilisé.")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 255, unique: true)]
    private ?string $username = null;
    
    #[ORM\Column(length: 255)]
    private ?string $password = null;
    
    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;
    
    #[ORM\Column]
    private ?float $solde = 0.0;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePicture = null;
    
    private ?string $plainPassword = null;

    // Nouveau champ pour le rôle sous forme d'entier : 0 par défaut, 1 correspond à admin.
    #[ORM\Column(type: "integer")]
    private int $role = 0;
    
    public function getId(): ?int
    {
        return $this->id;
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
    
    public function getPassword(): ?string
    {
        return $this->password;
    }
    
    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
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
    
    public function getSolde(): ?float
    {
        return $this->solde;
    }
    
    public function setSolde(float $solde): static
    {
        $this->solde = $solde;
        return $this;
    }
    
    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }
    
    public function setProfilePicture(?string $profilePicture): static
    {
        $this->profilePicture = $profilePicture;
        return $this;
    }
    
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }
    
    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }
    
    // Implémentation de UserInterface
    public function getRoles(): array
    {
        // Si $this->role vaut 1, on considère l'utilisateur comme admin.
        return $this->role === 1 ? ['ROLE_ADMIN'] : ['ROLE_USER'];
    }
    
    // Getter et setter pour le rôle en tant qu'entier
    public function getRole(): int
    {
        return $this->role;
    }
    
    public function setRole(int $role): static
    {
        $this->role = $role;
        return $this;
    }
    
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }
    
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }
}