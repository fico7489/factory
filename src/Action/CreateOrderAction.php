<?php

namespace App\Action;

use App\Entity\Order;
use App\Entity\User;
use App\Service\Order\OrderPlacer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreateOrderAction extends AbstractController
{
    public function __construct(
        private readonly OrderPlacer $orderPlacer,
        private readonly Security $security,
    ) {
    }

    public function __invoke(Request $request): Order
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $items = json_decode($request->getContent(), true)['items'] ?? [];

        return $this->orderPlacer->placeOrder($user, $items);
    }
}
