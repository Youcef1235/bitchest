<?php

namespace App\Entity;

use App\Repository\CryptocurrencyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CryptocurrencyRepository::class)]
class Cryptocurrency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 10, unique: true)]
    private ?string $symbol = null;

    #[ORM\OneToMany(mappedBy: 'cryptocurrency', targetEntity: Cotation::class, orphanRemoval: true)]
    private Collection $cotations;

    #[ORM\OneToMany(mappedBy: 'cryptocurrency', targetEntity: Purchase::class, orphanRemoval: true)]
    private Collection $purchases;

    public function __construct()
    {
        $this->cotations = new ArrayCollection();
        $this->purchases = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }
    public function getSymbol(): ?string { return $this->symbol; }
    public function setSymbol(string $symbol): static { $this->symbol = $symbol; return $this; }
    public function getCotations(): Collection { return $this->cotations; }
    public function getPurchases(): Collection { return $this->purchases; }
}