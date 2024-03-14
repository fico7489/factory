<?php

namespace App\Entity\Order;

use App\Entity\Adjustment\Adjustment;
use App\Entity\Order;
use App\Entity\Product;
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

    #[ORM\OneToOne(targetEntity: ProductPriceUser::class)]
    private ProductPriceUser $productPriceUser;

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

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getPriceAdjusted(): float
    {
        return $this->priceAdjusted;
    }

    public function setPriceAdjusted(float $priceAdjusted): void
    {
        $this->priceAdjusted = $priceAdjusted;
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function setDiscount(float $discount): void
    {
        $this->discount = $discount;
    }

    public function getTax(): float
    {
        return $this->tax;
    }

    public function setTax(float $tax): void
    {
        $this->tax = $tax;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): void
    {
        $this->total = $total;
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

    public function getAdjustments(): Collection
    {
        return $this->adjustments;
    }

    public function setAdjustments(Collection $adjustments): void
    {
        $this->adjustments = $adjustments;
    }

    public function getProductPriceUser(): ProductPriceUser
    {
        return $this->productPriceUser;
    }

    public function setProductPriceUser(ProductPriceUser $productPriceUser): void
    {
        $this->productPriceUser = $productPriceUser;
    }
}
