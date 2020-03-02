<?php

namespace App\Controller;

use App\Form\PasswordUpdateType;
use App\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     *
     * @return RedirectResponse|Response
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();
        $profileGeneralForm = $this->createForm(ProfileType::class, $user);
        $profileGeneralForm->handleRequest($request);

        $passwordUpdateForm = $this->createForm(PasswordUpdateType::class, $user);
        $passwordUpdateForm->handleRequest($request);

        /*
         * Process and update general profile information
         */
        if ($profileGeneralForm->isSubmitted() && $profileGeneralForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Your profile has been updated');

            return $this->redirect($this->generateUrl('profile'));
        }

        /*
         * Update password
         */
        if ($passwordUpdateForm->isSubmitted() && $passwordUpdateForm->isValid()) {
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Your password has been updated');

            return $this->redirect($this->generateUrl('profile'));
        }

        return $this->render('profile/index.html.twig', [
            'profileGenralForm' => $profileGeneralForm->createView(),
            'passwordUpdateForm' => $passwordUpdateForm->createView(),
        ]);
    }
}
