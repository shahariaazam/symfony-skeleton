<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
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
     * Link to this controller to start the "connect" process.
     *
     * @Route("/login/google", name="login_google")
     */
    public function loginWithGoogle(ClientRegistry $clientRegistry)
    {
        // It will redirect to Google
        return $clientRegistry
            ->getClient('google_oauth2') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect(['profile', 'email'], []);    // Required scope for Google OAuth2 login
    }

    /**
     * @Route("/login/google/check", name="login_google_check")
     */
    public function loginWithGoogleCheck()
    {
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
