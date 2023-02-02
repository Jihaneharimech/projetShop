<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    #[Route('/commade/create-session/{reference}', name: 'app_stripe_create_session')]
    public function index(EntityManagerInterface $entityManager, Cart $cart, $reference): Response
    {
        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);

        if (!$order) {
            new JsonResponse(['error' => 'order']);
        }

        foreach ($order->getOrderDetails()->getValues() as $product) {
            $product_object = $entityManager->getRepository(Product::class)->findOneByName($product->getProduct());
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN."/uploads/products/image/".$product_object->getIllustration()],
                    ],
                ],
                'quantity' => $product->getQuantity(),
            ];
        }

        $products_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ], 
            ],
            'quantity' => 1,
        ];

        \Stripe\Stripe::setApiKey('sk_test_51HWkIiB2UQ1rR5yt3j6uDXYObRD6UroL0zFgMoctO6fnbwArb7XDMacSJJErqupnaeTnwDKi5r78nIXjTHW5MMLL00EMJVfose');
        header('Content-Type: application/json');
  
        $checkout_session = \Stripe\Checkout\Session::create([
            'customer_email' => $this->getUser()->getEmail(),
          'line_items' => [[
            # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
            $products_for_stripe
          ]],
          'mode' => 'payment',
          'success_url' => $YOUR_DOMAIN . '/success.html',
          'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);

     
        $response = new JsonResponse(['id'=>$checkout_session->id]);       
        return $response;
    }
}
