<?php

namespace App\Entity;

use App\Entity\Adjustment\Adjustment;
use App\Entity\Order\OrderItem;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    private User $user;

    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'order')]
    private Collection $orderItems;

    #[ORM\OneToMany(targetEntity: Adjustment::class, mappedBy: 'order')]
    private Collection $adjustments;

    public function getId(): ?int
    {
        return $this->id;
    }

}
