<?php

namespace App\Entity;

use App\Repository\CotationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CotationRepository::class)]
class Cotation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'cotations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cryptocurrency $cryptocurrency = null;

    #[ORM\Column(type: 'decimal', precision: 15, scale: 2)]
    private ?string $price = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $quotedAt = null;

    public function getId(): ?int { return $this->id; }
    public function getCryptocurrency(): ?Cryptocurrency { return $this->cryptocurrency; }
    public function setCryptocurrency(?Cryptocurrency $cryptocurrency): static { $this->cryptocurrency = $cryptocurrency; return $this; }
    public function getPrice(): ?string { return $this->price; }
    public function setPrice(string $price): static { $this->price = $price; return $this; }
    public function getQuotedAt(): ?\DateTimeImmutable { return $this->quotedAt; }
    public function setQuotedAt(\DateTimeImmutable $quotedAt): static { $this->quotedAt = $quotedAt; return $this; }
}