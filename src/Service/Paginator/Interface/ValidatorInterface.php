<?php

namespace App\Service\Paginator\Interface;

interface ValidatorInterface
{
    public function validate(array $context): array;
}
