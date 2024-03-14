<?php

namespace App\Entity\Adjustment;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product\ContractList;
use App\Entity\Product\PriceList;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class PriceAdjustment
{
    public const TYPE_PRICE_LIST = 'price_list';
    public const TYPE_DISCOUNT = 'contract_list';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private ?string $type = null;

    #[ORM\ManyToOne(targetEntity: PriceList::class, inversedBy: 'priceAdjustments')]
    private PriceList $priceList;

    #[ORM\ManyToOne(targetEntity: ContractList::class, inversedBy: 'priceAdjustments')]
    private ContractList $contractList;

    public function getId(): ?int
    {
        return $this->id;
    }
}
