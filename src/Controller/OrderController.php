<?php

namespace App\Controller;



use Stripe\Stripe;
use App\Classe\Cart;
use App\Entity\Order;
use DateTimeImmutable;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    
    #[Route('/commande', name: 'app_order')]
    public function index(Cart $cart): Response
    {
        if (!$this->getUser()->getAdresses()->getValues())
        {
            return $this->redirectToRoute('app_account_address_add');
        }
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);
        return $this->render('order/index.html.twig',[
            'form' => $form->createView(),
            'cart' => $cart->getFull() 
        ]);
    }

    #[Route('/commande/recapitulatif', name: 'app_order_recap', methods: ['POST'])]
    public function add(Cart $cart, Request $request)
    {
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new DateTimeImmutable ();
            $carriers = $form->get('carriers')->getData();

            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getFirstname().' '.$delivery->getLastname();
            $delivery_content .= '<br/>'.$delivery->getPhone();

            if ($delivery->getCompany()) {
                $delivery_content .= '<br/>'.$delivery->getCompany();
            }

            $delivery_content .= '<br/>'.$delivery->getAdress();
            $delivery_content .= '<br/>'.$delivery->getPostal().' '.$delivery->getCity();
            $delivery_content .= '<br/>'.$delivery->getCounty();

            // Enregistrer ma commande Order()
            $order = new Order();
            $reference = $date->format('dmY').'-'.uniqid();
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setIsPaid(0);

            $this->entityManager->persist($order);

            $products_for_stripe = [];
            $YOUR_DOMAIN = 'http://127.0.0.1:8000';

            // Enregistrer mes produits OrderDetails()
            foreach ($cart->getFull() as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
                $this->entityManager->persist($orderDetails);

                $products_for_stripe[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => $product['product']->getPrice(),
                        'product_data' => [
                            'name' => $product['product']->getName(),
                            'images' => [$YOUR_DOMAIN."/uploads/products/image/".$product['product']->getIllustration()],
                        ],
                    ],
                    'quantity' => $product['quantity'],
                ];
        
            }
     
           // $this->entityManager->flush();

                \Stripe\Stripe::setApiKey('sk_test_51HWkIiB2UQ1rR5yt3j6uDXYObRD6UroL0zFgMoctO6fnbwArb7XDMacSJJErqupnaeTnwDKi5r78nIXjTHW5MMLL00EMJVfose');
                header('Content-Type: application/json');
          
                $checkout_session = \Stripe\Checkout\Session::create([
                  'line_items' => [[
                    # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                    $products_for_stripe
                  ]],
                  'mode' => 'payment',
                  'success_url' => $YOUR_DOMAIN . '/success.html',
                  'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
                ]);
                header("HTTP/1.1 303 See Other");
                header("Location: " . $checkout_session->url);

            return $this->render('order/add.html.twig', [
                'cart' => $cart->getFull(),
                'carrier' => $carriers,
                'delivery' => $delivery_content,
                'stripe_checkout_session' => $checkout_session->id
            ]);
        }

        return $this->redirectToRoute('app_cart');
    }

}
