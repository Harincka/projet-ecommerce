<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/login", name="app_signin")
     */
    public function loginAction()
    {
        $utils = $this->get('security.authentication_utils');

        return $this->render('login.html.twig', [
            'last_username' => $utils->getLastUsername(),
            'error' => $utils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/login-check", name="app_check_signin")
     */
    public function loginCheckAction()
    {

    }


    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            // On enregistre l'utilisateur en base
            $em = $this->getDoctrine()->getManager();

            $encodedPassword = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());
            $user->setPassword($encodedPassword);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}