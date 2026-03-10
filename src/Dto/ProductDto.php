<?php

namespace App\Dto;

use App\Enum\Currency;
use App\Enum\Status;
use Symfony\Component\Validator\Constraints as Assert;
class ProductDto
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $name = null;
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $sku = null;
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?float $price = null;
    #[Assert\NotBlank]
    #[Assert\Choice(
        callback: [Currency::class, 'values'],
        message: 'Currency must be one of: PLN, USD, EUR'
    )]
    private ?string $currency = null;
    #[Assert\NotBlank]
    #[Assert\Choice(
        callback: [Status::class, 'values'],
        message: 'Status must be one of: active, inactive'
    )]
    private ?string $status = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(?string $sku): void
    {
        $this->sku = $sku;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }
}
