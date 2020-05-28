<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\PasswordAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    /**
     * @Route("/signup", name="app_signup")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, PasswordAuthenticator $authenticator, MailerInterface $mailer): Response
    {
        // If user is already logged in, they will be redirected to homepage
        if ($this->getUser()) {
            $this->addFlash('warning', 'You were already logged in');

            return $this->redirectToRoute('app_homepage');
        }

        // Check whether registration is not diabled
        if ('true' === $this->getParameter('app_signup_disabled')) {
            return $this->render('error_layout.html.twig', [
                'code' => 'Ooops!',
                'status' => 'Service Disabled',
                'description' => 'Registration service has been disabled temporarily. Please come back later.',
            ]);
        }

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

            $user->setIsEmailVerified(false);
            $user->setEmailVerificationToken(uuid_create(UUID_TYPE_DCE));
            $user->setEmailVerificationTokenExpiredAt((new \DateTime())->add(new \DateInterval('PT24H')));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // Send email verification link
            $email = (new TemplatedEmail())
                ->from(new Address($this->getParameter('email_from'), $this->getParameter('email_from_name')))
                ->to($user->getEmail())
                ->context(['user' => $user, 'verification_link' => $request->getSchemeAndHttpHost().'/verify-email?token='.$user->getEmailVerificationToken()])
                ->subject('Signup completed. Verify email address')
                ->htmlTemplate('emails/signup_confirmation.html.twig');

            $mailer->send($email);

            $this->addFlash('success', 'Please check your inbox to verify email address');

            return $this->redirectToRoute('app_login');
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
        // If user is already logged in, they will be redirected to homepage
        if ($this->getUser()) {
            $this->addFlash('warning', 'You were already logged in');

            return $this->redirectToRoute('app_homepage');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/reset-password", name="reset_password")
     */
    public function resetPassword(): Response
    {
        // If user is already logged in, they will be redirected to homepage
        if ($this->getUser()) {
            $this->addFlash('warning', 'You were already logged in');

            return $this->redirectToRoute('change_password');
        }

        return $this->render('auth/reset_password.html.twig');
    }

    /**
     * @Route("/verify-email", name="verify_email")
     */
    public function verifyEmail(Request $request, UserRepository $repository): Response
    {
        $token = $request->query->get('token');
        if (empty($token)) {
            $this->addFlash('warning', 'Invalid verification token');

            return $this->redirectToRoute('app_login');
        }

        // If user is already logged in, they will be redirected to homepage
        if ($this->getUser() && $this->getUser()->getIsEmailVerified()) {
            $this->addFlash('warning', 'Your email address was already verified');

            return $this->redirectToRoute('profile');
        }

        $user = $repository->findOneBy(['email_verification_token' => $token]);
        if (empty($user)) {
            $this->addFlash('warning', 'Invalid verification token');

            return $this->redirectToRoute('app_login');
        }

        $user->setIsEmailVerified(true);
        $user->setEmailVerificationTokenExpiredAt(null);
        $user->setEmailVerificationToken(null);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Email address verification successful');

        return $this->redirectToRoute('app_login');
    }

    /**
     * Link to this controller to start the "connect" process.
     *
     * @Route("/login/google", name="login_google")
     */
    public function loginWithGoogle(ClientRegistry $clientRegistry)
    {
        if ('true' === $this->getParameter('app_signup_disabled')) {
            return $this->render('error_layout.html.twig', [
                'code' => 'Ooops!',
                'status' => 'Service Disabled',
                'description' => 'Registration service has been disabled temporarily. Please come back later.',
            ]);
        }

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
