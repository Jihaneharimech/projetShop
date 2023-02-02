<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountOrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/mes-commandes', name: 'app_account_order')]
    public function index(): Response
    {
        $orders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($this->getUser());
        
        return $this->render('account/order.html.twig',[
            'orders' => $orders
        ]);
    }

    #[Route('/compte/mes-commandes/{id}', name: 'app_account_order_show')]
    public function show($id): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneById($id);
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_account_order');
        }

        return $this->render('account/order_show.html.twig', [
            'order' => $order
        ]);
    }

}
