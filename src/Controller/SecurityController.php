<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/registration", name="registration")
     */
    public function registration(Request $req, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder){

        $user = new User;
        $formUser = $this->createForm(RegistrationType::class,$user);
        $formUser->handleRequest($req);
        if($formUser->isSubmitted() && $formUser->isValid()){
            if($formUser->get('password')->getData() != $formUser->get('confirmedPassword')->getData()){
                $this->addFlash('error', 'Password and confirmed password must be the same');
                return $this->render('security/registration.html.twig',[
                    'form' => $formUser->createView()
                ]); 
            }
            $user->setPassword(sha1($formUser->get('password')->getData()));
            $em->persist($user);
            $em->flush();
        }
        return $this->render('security/registration.html.twig',[
            'form' => $formUser->createView()
        ]);
    }
}
