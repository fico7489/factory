<?php

namespace App\Entity\Product;

use App\Entity\Adjustment\PriceAdjustment;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ContractList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'float')]
    private ?float $price = null;

    #[ORM\Column(type: 'string')]
    private ?string $sku = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'contractLists')]
    private User $user;

    #[ORM\OneToMany(targetEntity: PriceAdjustment::class, mappedBy: 'contractList')]
    private Collection $priceAdjustments;

    public function getId(): ?int
    {
        return $this->id;
    }
}
