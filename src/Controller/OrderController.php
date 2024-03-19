<?php

namespace App\Controller;

use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    #[Route('/orders/{id}', name: 'app_orders_show')]
    public function show(Order $order)
    {
        return $this->render('order/show.html.twig', ['order' => $order]);
    }
}
