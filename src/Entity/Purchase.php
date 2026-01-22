<?php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
class Purchase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cryptocurrency $cryptocurrency = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 8)]
    private ?string $quantity = null;

    #[ORM\Column(type: 'decimal', precision: 15, scale: 2)]
    private ?string $priceAtPurchase = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $purchasedAt = null;

    public function getId(): ?int { return $this->id; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }
    public function getCryptocurrency(): ?Cryptocurrency { return $this->cryptocurrency; }
    public function setCryptocurrency(?Cryptocurrency $cryptocurrency): static { $this->cryptocurrency = $cryptocurrency; return $this; }
    public function getQuantity(): ?string { return $this->quantity; }
    public function setQuantity(string $quantity): static { $this->quantity = $quantity; return $this; }
    public function getPriceAtPurchase(): ?string { return $this->priceAtPurchase; }
    public function setPriceAtPurchase(string $priceAtPurchase): static { $this->priceAtPurchase = $priceAtPurchase; return $this; }
    public function getPurchasedAt(): ?\DateTimeImmutable { return $this->purchasedAt; }
    public function setPurchasedAt(\DateTimeImmutable $purchasedAt): static { $this->purchasedAt = $purchasedAt; return $this; }
}