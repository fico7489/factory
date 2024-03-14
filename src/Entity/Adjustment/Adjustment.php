<?php

namespace App\Entity\Adjustment;

use App\Entity\Order;
use App\Entity\Order\OrderItem;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Adjustment
{
    public const TYPE_PRICE = 'price';
    public const TYPE_DISCOUNT = 'discount';
    public const TYPE_TAX = 'tax';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private ?string $type = null;

    #[ORM\Column(type: 'float')]
    private ?float $value = null;

    #[ORM\ManyToOne(targetEntity: DiscountAdjustment::class)]
    private DiscountAdjustment $discountAdjustment;

    #[ORM\ManyToOne(targetEntity: TaxAdjustment::class)]
    private TaxAdjustment $taxAdjustment;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'adjustments')]
    private Order $order;

    #[ORM\ManyToOne(targetEntity: OrderItem::class, inversedBy: 'adjustments')]
    private OrderItem $orderItem;

    public function getId(): ?int
    {
        return $this->id;
    }
}
