<?php

namespace App\Entity\Order;

use App\Entity\Product\ContractList;
use App\Entity\Product\PriceList;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ProductPriceUser
{
    public const TYPE_PRODUCT = 'product';
    public const TYPE_PRICE_LIST = 'price_list';
    public const TYPE_CONTRACT_LIST = 'contract_list';

    public function __construct(
        float $price,
        string $type,
        ?PriceList $priceList = null,
        ?ContractList $contractList = null,
    ) {
        $this->price = $price;
        $this->type = $type;
        $this->priceList = $priceList;
        $this->contractList = $contractList;
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'float')]
    private float $price;

    #[ORM\Column(type: 'string')]
    private ?string $type = null;

    #[ORM\OneToOne(targetEntity: OrderItem::class)]
    private OrderItem $orderItem;

    #[ORM\ManyToOne(targetEntity: PriceList::class, inversedBy: 'orderItemPrices')]
    private ?PriceList $priceList;

    #[ORM\ManyToOne(targetEntity: ContractList::class, inversedBy: 'orderItemPrices')]
    private ?ContractList $contractList;

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

    public function getPriceList(): ?PriceList
    {
        return $this->priceList;
    }

    public function setPriceList(?PriceList $priceList): void
    {
        $this->priceList = $priceList;
    }

    public function getContractList(): ?ContractList
    {
        return $this->contractList;
    }

    public function setContractList(?ContractList $contractList): void
    {
        $this->contractList = $contractList;
    }
}
