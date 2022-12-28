<?php

namespace App\Controller;


use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountPasswordController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/modifier-mot-de-passe', name: 'app_account_password')]
    public function index(Request $request, UserPasswordEncoderInterface $encoder ): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class,$user);
        $notification= null;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $old_password = $form->get('old_password')->getData();

            if( $encoder->isPasswordValid($user, $old_password)){
                $new_password = $form->get('new_password')->getData(); 
                $password= $encoder->encodePassword($user, $new_password);
            
                $user->setPassword($password);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $notification= 'Votre mot de passe a ete bien mis à jour';
            }else {
                $notification = 'Le mot de passe actual ne pas le bon';
            }
        }
        
        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
             'notification' => $notification
        ]);
    }
}
