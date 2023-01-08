<?php

namespace App\Controller;

use App\Entity\Product;
use App\Classe\SearchCategorie;
use App\Form\SearchCategorieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{

    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    //Affichage list des produits
    #[Route('/nos-produits', name: 'app_products')]
    public function index(Request $request): Response
    {

        $searchcategorie = new SearchCategorie();
        $form = $this->createForm(SearchCategorieType::class,$searchcategorie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
         //Recuperer les produit en fanction de mes recherche
         $produits=$this->entityManager->getRepository(Product::class)->findWithSearch($searchcategorie);      
        } else {
            $produits=$this->entityManager->getRepository(Product::class)->findAll();
        }
        return $this->render('product/index.html.twig',[
            'produits' => $produits,
            'form' => $form->createView()

        ]);
    }

    //Voir le detail d'un produit
    #[Route('/produit/{slug}', name: 'app_product')]
    public function show($slug): Response
    {
        $produit=$this->entityManager->getRepository(Product::class)->findOneBySlug($slug);
        if(!$produit){
            return $this->redirectToRoute('app_products');
        }
        return $this->render('product/show.html.twig',[
            'produits' => $produit
        ]);
    }

}
