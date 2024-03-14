<?php

namespace App\Entity\Product;

use App\Entity\Order\Price\PriceItem;
use App\Entity\UserGroup;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class PriceList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'float')]
    private ?float $price = null;

    #[ORM\Column(type: 'string')]
    private ?string $sku = null;

    #[ORM\ManyToOne(targetEntity: UserGroup::class, inversedBy: 'priceLists')]
    private UserGroup $userGroup;

    #[ORM\OneToMany(targetEntity: PriceItem::class, mappedBy: 'priceList')]
    private Collection $orderItemPrices;

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

    public function getUserGroup(): UserGroup
    {
        return $this->userGroup;
    }

    public function setUserGroup(UserGroup $userGroup): void
    {
        $this->userGroup = $userGroup;
    }
}
