<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: "text")]
    private ?string $description = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private ?string $prix = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $datePublication = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\User")]
    #[ORM\JoinColumn(nullable: true)]
    private ?\App\Entity\User $auteur = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\Choice(choices: ["Homme", "Femme", "Enfant", "Mixte", "Accessoires"], message: "Choisissez un type valide.")]
    private ?string $type = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $imageUrl = null;

    // Champ pour les tailles (facultatif)
    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $tailles = null;

    // Champ pour la marque (facultatif)
    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $brand = null;

    // Champ pour indiquer si c'est une nouveauté (true = neuf, false = occasion)
    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $nouveaute = false;

    // Champ pour la quantité
    #[ORM\Column(type: "integer", options: ["default" => 1])]
    private ?int $quantite = 1;

    // Champ pour l'état ("Neuf" ou "Occasion")
    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private ?string $etat = null;

    /**
     * Champ dédié pour différencier un article créé par l'admin d'une annonce utilisateur.
     * true => créé par l'admin, false => créé par un utilisateur lambda.
     */
    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $createdByAdmin = false;

    // Getters et setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->datePublication;
    }

    public function setDatePublication(\DateTimeInterface $datePublication): self
    {
        $this->datePublication = $datePublication;
        return $this;
    }

    public function getAuteur(): ?\App\Entity\User
    {
        return $this->auteur;
    }

    public function setAuteur(?\App\Entity\User $auteur): self
    {
        $this->auteur = $auteur;
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

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function getTailles(): ?string
    {
        return $this->tailles;
    }

    public function setTailles(?string $tailles): self
    {
        $this->tailles = $tailles;
        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;
        return $this;
    }

    public function getNouveaute(): bool
    {
        return $this->nouveaute;
    }

    public function setNouveaute(bool $nouveaute): self
    {
        $this->nouveaute = $nouveaute;
        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;
        return $this;
    }

    public function isCreatedByAdmin(): bool
    {
        return $this->createdByAdmin;
    }

    public function setCreatedByAdmin(bool $createdByAdmin): self
    {
        $this->createdByAdmin = $createdByAdmin;
        return $this;
    }
}