<?php

namespace App\Entity;

use App\Repository\PriceHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PriceHistoryRepository::class)]
class PriceHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'priceHistories')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')]
    private ?Product $product = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['product:read'])]
    private ?string $old_price = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['product:read'])]
    private ?string $new_price = null;

    #[ORM\Column]
    #[Groups(['product:read'])]
    private ?\DateTimeImmutable $changed_at;

    public function __construct()
    {
        $this->changed_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductId(): ?Product
    {
        return $this->product;
    }

    public function setProductId(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getOldPrice(): ?string
    {
        return $this->old_price;
    }

    public function setOldPrice(string $old_price): static
    {
        $this->old_price = $old_price;

        return $this;
    }

    public function getNewPrice(): ?string
    {
        return $this->new_price;
    }

    public function setNewPrice(string $new_price): static
    {
        $this->new_price = $new_price;

        return $this;
    }

    public function getChangedAt(): ?\DateTimeImmutable
    {
        return $this->changed_at;
    }

    public function setChangedAt(\DateTimeImmutable $changed_at): static
    {
        $this->changed_at = $changed_at;

        return $this;
    }
}
