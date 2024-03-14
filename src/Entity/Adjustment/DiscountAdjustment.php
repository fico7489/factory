<?php

namespace App\Entity\Adjustment;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class DiscountAdjustment
{
    public const TYPE_LOYAL_CUSTOMER = 'loyal_customer';
    public const TYPE_AMOUNT = 'amount';
    public const TYPE_FIXED_ORDER_ITEM = 'fixed_order_item';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private ?string $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
