<?php

namespace App\Service\Paginator\Interface;

// interface for validating data for each paginator
interface ValidatorInterface
{
    public function validate(array $context): array;
}
