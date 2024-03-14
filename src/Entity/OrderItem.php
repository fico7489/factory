<?php

namespace App\Entity;

use App\Entity\Adjustment\Adjustment;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    #[ORM\Column(type: 'float')]
    private float $price;

    #[ORM\Column(type: 'float')]
    private float $priceAdjusted;

    #[ORM\Column(type: 'float')]
    private float $discount;

    #[ORM\Column(type: 'float')]
    private float $tax;

    #[ORM\Column(type: 'float')]
    private float $total;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderItems')]
    private Order $order;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'orderItems')]
    private Product $product;

    #[ORM\OneToMany(targetEntity: Adjustment::class, mappedBy: 'orderItem')]
    private Collection $adjustments;

    public function getId(): ?int
    {
        return $this->id;
    }
}
