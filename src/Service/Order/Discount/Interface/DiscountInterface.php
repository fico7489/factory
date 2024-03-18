<?php

namespace App\Service\Order\Discount\Interface;

use App\Entity\Order;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.discount.provider')]
interface DiscountInterface
{
    public function name(): string;

    public function match(Order $order): bool;

    public function apply(Order $order): void;
}
