<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ResertPasswordController extends AbstractController
{
    #[Route('/mot-de-passe-oublie', name: 'app_resert_password')]
    public function index(Request $request): Response
    {
        //Redireger les users connecter vers la page home
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        if ($request->get('email')) {
        dd($request->get('email'));
        }
        return $this->render('resert_password/index.html.twig');
    }
}
