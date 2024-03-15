<?php

namespace App\Service\Tax\Interface;

use App\Entity\Order;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.tax.provider')]
interface TaxInterface
{
    public function name(): string;

    public function match(Order $order): bool;

    public function apply(Order $order): void;
}
