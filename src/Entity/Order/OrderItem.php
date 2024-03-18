<?php

namespace App\Entity\Order;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Entity\Order;
use App\Entity\Order\Price\OrderItemPrice;
use App\Entity\Product;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(
    operations: [
        new Get(),
    ],
)]
#[ORM\Entity]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $price = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $priceAdjusted = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $subtotal = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $discountGlobal = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $discountItem = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $tax = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $total = null;

    #[ORM\OneToOne(targetEntity: OrderItemPrice::class)]
    private ?OrderItemPrice $priceItem = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderItems')]
    private Order $order;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'orderItems')]
    private Product $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiscount(): ?float
    {
        return $this->getDiscountItem() + $this->getDiscountGlobal();
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    public function getPriceAdjusted(): ?float
    {
        return $this->priceAdjusted;
    }

    public function setPriceAdjusted(?float $priceAdjusted): void
    {
        $this->priceAdjusted = $priceAdjusted;
    }

    public function getSubtotal(): ?float
    {
        return $this->subtotal;
    }

    public function setSubtotal(?float $subtotal): void
    {
        $this->subtotal = $subtotal;
    }

    public function getDiscountGlobal(): ?float
    {
        return $this->discountGlobal;
    }

    public function setDiscountGlobal(?float $discountGlobal): void
    {
        $this->discountGlobal = $discountGlobal;
    }

    public function getTax(): ?float
    {
        return $this->tax;
    }

    public function setTax(?float $tax): void
    {
        $this->tax = $tax;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(?float $total): void
    {
        $this->total = $total;
    }

    public function getPriceItem(): ?OrderItemPrice
    {
        return $this->priceItem;
    }

    public function setPriceItem(?OrderItemPrice $priceItem): void
    {
        $this->priceItem = $priceItem;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function getDiscountItem(): ?float
    {
        return $this->discountItem;
    }

    public function setDiscountItem(?float $discountItem): void
    {
        $this->discountItem = $discountItem;
    }
}
