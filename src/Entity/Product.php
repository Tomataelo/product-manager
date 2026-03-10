<?php

namespace App\Entity;

use App\Enum\Currency;
use App\Enum\Status;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product:read', 'product:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product:read', 'product:write'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['product:read', 'product:write'])]
    private ?string $sku = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['product:read', 'product:write'])]
    private ?string $price = null;

    #[ORM\Column(type: 'string', length: 3, enumType: Currency::class)]
    #[Groups(['product:read', 'product:write'])]
    private ?Currency $currency = null;

    #[ORM\Column(type: 'string', length: 50, enumType: Status::class)]
    #[Groups(['product:read', 'product:write'])]
    private ?Status $status = null;

    /**
     * @var Collection<int, PriceHistory>
     */
    #[ORM\OneToMany(targetEntity: PriceHistory::class, mappedBy: 'product')]
    #[Groups(['product:read'])]
    private Collection $priceHistories;

    #[ORM\Column]
    private ?bool $is_deleted = false;

    #[ORM\Column]
    #[Groups(['product:read', 'product:write'])]
    private ?\DateTimeImmutable $created_at;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->priceHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): static
    {
        $this->sku = $sku;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection<int, PriceHistory>
     */
    public function getPriceHistories(): Collection
    {
        return $this->priceHistories;
    }

    public function addPriceHistory(PriceHistory $priceHistory): static
    {
        if (!$this->priceHistories->contains($priceHistory)) {
            $this->priceHistories->add($priceHistory);
            $priceHistory->setProductId($this);
        }

        return $this;
    }

    public function removePriceHistory(PriceHistory $priceHistory): static
    {
        if ($this->priceHistories->removeElement($priceHistory)) {
            // set the owning side to null (unless already changed)
            if ($priceHistory->getProductId() === $this) {
                $priceHistory->setProductId(null);
            }
        }

        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->is_deleted;
    }

    public function setIsDeleted(bool $is_deleted): static
    {
        $this->is_deleted = $is_deleted;

        return $this;
    }
}
