<?php

namespace App\Entity\Product;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(
    operations: [
        new Get(),
    ],
)]
#[ORM\Entity]
#[ORM\Index(name: 'search', columns: ['user_id', 'sku'])]
class ProductContractList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'float')]
    private ?float $price = null;

    #[ORM\Column(type: 'string')]
    private ?string $sku = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(?string $sku): void
    {
        $this->sku = $sku;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
