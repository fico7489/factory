<?php

namespace App\Entity\Order\Price;

use App\Entity\Order\OrderItem;
use App\Entity\Product\ProductContractList;
use App\Entity\Product\ProductPriceList;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class OrderItemPrice
{
    public const TYPE_PRODUCT = 'product';
    public const TYPE_PRICE_LIST = 'price_list';
    public const TYPE_CONTRACT_LIST = 'contract_list';

    public function __construct(
        int $productId,
        float $price,
        string $type,
        ?ProductPriceList $priceList = null,
        ?ProductContractList $contractList = null,
    ) {
        $this->productId = $productId;
        $this->price = $price;
        $this->type = $type;
        $this->priceList = $priceList;
        $this->contractList = $contractList;
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    private float $price;
    private int $productId;

    #[ORM\Column(type: 'string')]
    private ?string $type = null;

    #[ORM\OneToOne(targetEntity: OrderItem::class)]
    private OrderItem $orderItem;

    #[ORM\ManyToOne(targetEntity: ProductPriceList::class, inversedBy: 'orderItemPrices')]
    private ?ProductPriceList $priceList;

    #[ORM\ManyToOne(targetEntity: ProductContractList::class, inversedBy: 'orderItemPrices')]
    private ?ProductContractList $contractList;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): void
    {
        $this->productId = $productId;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getOrderItem(): OrderItem
    {
        return $this->orderItem;
    }

    public function setOrderItem(OrderItem $orderItem): void
    {
        $this->orderItem = $orderItem;
    }

    public function getPriceList(): ?ProductPriceList
    {
        return $this->priceList;
    }

    public function setPriceList(?ProductPriceList $priceList): void
    {
        $this->priceList = $priceList;
    }

    public function getContractList(): ?ProductContractList
    {
        return $this->contractList;
    }

    public function setContractList(?ProductContractList $contractList): void
    {
        $this->contractList = $contractList;
    }
}
