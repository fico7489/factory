<?php

namespace App\Entity\Product;

use App\Entity\Adjustment\PriceAdjustment;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class PriceList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private ?string $name = null;

    #[ORM\Column(type: 'float')]
    private ?float $price = null;

    #[ORM\Column(type: 'string')]
    private ?string $sku = null;

    #[ORM\OneToMany(targetEntity: PriceAdjustment::class, mappedBy: 'priceList')]
    private Collection $priceAdjustments;

    public function getId(): ?int
    {
        return $this->id;
    }
}
