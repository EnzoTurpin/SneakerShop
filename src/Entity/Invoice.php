<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateTransaction = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $montant = null;

    #[ORM\Column(length: 255)]
    private ?string $adresseFacturation = null;

    #[ORM\Column(length: 100)]
    private ?string $villeFacturation = null;

    #[ORM\Column(length: 20)]
    private ?string $codePostalFacturation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateTransaction(): ?\DateTimeInterface
    {
        return $this->dateTransaction;
    }

    public function setDateTransaction(\DateTimeInterface $dateTransaction): static
    {
        $this->dateTransaction = $dateTransaction;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getAdresseFacturation(): ?string
    {
        return $this->adresseFacturation;
    }

    public function setAdresseFacturation(string $adresseFacturation): static
    {
        $this->adresseFacturation = $adresseFacturation;

        return $this;
    }

    public function getVilleFacturation(): ?string
    {
        return $this->villeFacturation;
    }

    public function setVilleFacturation(string $villeFacturation): static
    {
        $this->villeFacturation = $villeFacturation;

        return $this;
    }

    public function getCodePostalFacturation(): ?string
    {
        return $this->codePostalFacturation;
    }

    public function setCodePostalFacturation(string $codePostalFacturation): static
    {
        $this->codePostalFacturation = $codePostalFacturation;

        return $this;
    }
}
