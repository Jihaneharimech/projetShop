<?php

namespace App\Controller;

use App\Entity\Adress;
use App\Form\AddressType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountAddressController extends AbstractController
{
    #[Route('/compte/adresses', name: 'app_account_address')]
    public function index(): Response
    {
        return $this->render('account/address.html.twig');
    }

    //Ajouter une addresse
    #[Route('/compte/ajouter-une-adresse', name: 'app_account_address_add')]
    public function add()
    {

        $address = new Adress();
        $form = $this->createForm(AddressType::class, $address);

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
