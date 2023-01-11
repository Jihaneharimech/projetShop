<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/panier', name: 'app_cart')]
    public function index(Cart $cart): Response
    { 
        return $this->render('cart/index.html.twig',[
            'cart' => $cart->getFull()
        ]);
    }
    
    //Fonction pour ajouter un produit au panier 
    #[Route('/cart/{id}', name: 'app_add_cart')]
    public function add(Cart $cart,$id): Response
    {
        $cart->add($id);
        return $this->redirectToRoute('app_cart');
    }

    //Fonction pour vider la sission
    #[Route('/cart/remove', name: 'app_remove_cart')]
    public function remove(Cart $cart)
    {
        $cart->remove();
        return $this->redirectToRoute('app_products');
    }

    //Supprimer un produit dans la cart
    #[Route('/cart/delete/{id}', name: 'app_delete_cart')]
    public function delete(Cart $cart, $id)
    {
        $cart->delete($id);
        return $this->redirectToRoute('app_cart');
    }

    //diminuer la quantite de produit
    #[Route('/cart/decrease/{id}', name: 'app_decrease_cart')]
    public function decrease(Cart $cart, $id)
    {
        $cart->decrease($id);
        return $this->redirectToRoute('app_cart');
    }


}
