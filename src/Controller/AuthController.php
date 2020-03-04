<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\PasswordAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    /**
     * @Route("/signup", name="app_signup")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, PasswordAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('auth/signup.html.twig', [
            'form' => $form->createView(),
            'error' => null,
        ]);
    }

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

        return $this->render('auth/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
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
     *
     * @codeCoverageIgnoreStart
     */
    public function loginWithGoogleCheck()
    {
    }

    // @codeCoverageIgnoreEnd

    /**
     * @Route("/logout", name="app_logout")
     *
     * @codeCoverageIgnoreStart
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    // @codeCoverageIgnoreEnd
}
