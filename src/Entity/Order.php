<?php

namespace App\Entity;

use App\Entity\Adjustment\Adjustment;
use App\Entity\Order\OrderItem;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`order`')]
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

    public function getSubtotal()
    {
        $subtotal = 0;

        foreach ($this->getOrderItems() as $orderItem) {
            $subtotal += $orderItem->getPriceAdjusted();
        }

        return $subtotal;
    }

    public function getDiscount()
    {
        $discount = 0;

        foreach ($this->getOrderItems() as $orderItem) {
            $discount += $orderItem->getDiscount();
        }

        return $discount;
    }

    public function getTax()
    {
        $tax = 0;

        foreach ($this->getOrderItems() as $orderItem) {
            $tax += $orderItem->getTax();
        }

        return $tax;
    }

    public function getTotal()
    {
        $total = 0;

        foreach ($this->getOrderItems() as $orderItem) {
            $total += $orderItem->getTotal();
        }

        return $total;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /** @return  Collection<OrderItem> */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function setOrderItems(Collection $orderItems): void
    {
        $this->orderItems = $orderItems;
    }

    public function getAdjustments(): Collection
    {
        return $this->adjustments;
    }

    public function setAdjustments(Collection $adjustments): void
    {
        $this->adjustments = $adjustments;
    }
}
