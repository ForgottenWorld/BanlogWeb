<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\User;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
	
	/**
     * @Route("/changePassword", name="app_change_password")
     */
    public function changePassword(Request $request, AuthenticationUtils $authenticationUtils, UserPasswordEncoderInterface $passwordEncoder, UrlGeneratorInterface $urlGenerator): Response
    {
		$this->denyAccessUnlessGranted('ROLE_USER');
		
		$entityManager = $this -> getDoctrine() -> getManager();
		$user = $this->getUser();
		
		$newPassword 		= $request -> get('new_password');
		
		$user->setPassword($passwordEncoder->encodePassword(
			$user,
			$newPassword
		));
		
		$entityManager -> persist($user);
		$entityManager -> flush();

        return new RedirectResponse($urlGenerator->generate('homepage', array('successMessage' => "Password modificata con successo")));
    }
	
	/**
     * @Route("/newUser", name="app_new_user")
     */
    public function newUser(Request $request, AuthenticationUtils $authenticationUtils, UserPasswordEncoderInterface $passwordEncoder, UrlGeneratorInterface $urlGenerator): Response
    {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		
		$entityManager = $this -> getDoctrine() -> getManager();
		$user = $this->getUser();
		
		$username = $request -> get('username');
		$password = $request -> get('password');
		$role 	  = $request -> get('role');
		
		$user = new User();
		$user -> setUsername($username);
		$user -> setPassword($passwordEncoder->encodePassword(
			$user,
			$password
		));
		$user -> setRoles([$role]);
		
		$entityManager -> persist($user);
		$entityManager -> flush();

        return new RedirectResponse($urlGenerator->generate('homepage', array('successMessage' => "Utente aggiunto con successo")));
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
