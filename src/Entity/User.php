<?php

namespace App\Entity;

use App\Entity\Product\ContractList;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private ?string $firstName = null;

    #[ORM\Column(type: 'string')]
    private ?string $lastName = null;

    #[ORM\Column(type: 'string')]
    protected ?string $email;

    #[ORM\Column(type: 'string')]
    protected ?string $password;

    #[ORM\OneToMany(targetEntity: ContractList::class, mappedBy: 'user')]
    private Collection $contractLists;

    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'user')]
    private Collection $orders;

    public function getId(): ?int
    {
        return $this->id;
    }
}
