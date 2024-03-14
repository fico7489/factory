<?php

namespace App\Entity\Adjustment;

use App\Entity\Product\ContractList;
use App\Entity\Product\PriceList;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class TaxAdjustment
{
    public const TYPE_PDV = 'pdv';
    public const TYPE_GREEN = 'zeleni';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private ?string $type = null;

    #[ORM\Column(type: 'float')]
    private ?float $base = null;

    #[ORM\Column(type: 'float')]
    private ?float $rate = null;

    #[ORM\Column(type: 'float')]
    private ?float $value = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
