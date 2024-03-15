<?php

namespace App\Entity\Order\Tax;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class OrderItemTax
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
