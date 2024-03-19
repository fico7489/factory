<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Action\CreateOrderAction;
use App\Entity\Order\OrderItem;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(
    operations: [
        new Post(
            controller: CreateOrderAction::class,
        ),
    ],
)]
#[ORM\Entity]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $addressAddress = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $addressCity = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $addressCountry = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $addressPhone = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    private User $user;

    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'order')]
    private Collection $orderItems;

    public function getSubtotal(): float
    {
        return $this->sumOrderItem('getSubtotal');
    }

    public function getDiscount(): float
    {
        return $this->sumOrderItem('getDiscount');
    }

    public function getTax(): float
    {
        return $this->sumOrderItem('getTax');
    }

    public function getTotal(): float
    {
        return $this->sumOrderItem('getTotal');
    }

    private function sumOrderItem($getter)
    {
        $sum = 0;

        foreach ($this->getOrderItems() as $orderItem) {
            $sum += $orderItem->{$getter}();
        }

        return $sum;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddressAddress(): ?string
    {
        return $this->addressAddress;
    }

    public function setAddressAddress(?string $addressAddress): void
    {
        $this->addressAddress = $addressAddress;
    }

    public function getAddressCity(): ?string
    {
        return $this->addressCity;
    }

    public function setAddressCity(?string $addressCity): void
    {
        $this->addressCity = $addressCity;
    }

    public function getAddressCountry(): ?string
    {
        return $this->addressCountry;
    }

    public function setAddressCountry(?string $addressCountry): void
    {
        $this->addressCountry = $addressCountry;
    }

    public function getAddressPhone(): ?string
    {
        return $this->addressPhone;
    }

    public function setAddressPhone(?string $addressPhone): void
    {
        $this->addressPhone = $addressPhone;
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
}
