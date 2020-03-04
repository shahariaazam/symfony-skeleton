<?php

namespace App\Controller;

use App\Form\PasswordUpdateType;
use App\Form\ProfilePictureType;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

        //var_dump($user->getImageFile());
        //die();

        $profilePictureForm = $this->createForm(ProfilePictureType::class, $user);
        $profilePictureForm->handleRequest($request);

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

        /*
         * Process and update profile picture
         *
         * @TODO we can create a service class to achive this and keep controller class clean
         */
        if ($profilePictureForm->isSubmitted() && $profilePictureForm->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $profilePictureForm->get('picture')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
                    $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('profile_picture_upload_dir'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Failed to upload your profile picture. Please try again later');
                }

                $user->setProfilePicture($newFilename);

                // Update the profile picture path
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Profile picture has been changed successfully');

                return $this->redirect($this->generateUrl('profile'));
            }
        }

        return $this->render('profile/index.html.twig', [
            'profileGenralForm' => $profileGeneralForm->createView(),
            'passwordUpdateForm' => $passwordUpdateForm->createView(),
            'profilePictureForm' => $profilePictureForm->createView(),
        ]);
    }

    /**
     * @Route("/p/{user_slug}", name="profile_public_url")
     *
     * @return RedirectResponse|Response
     */
    public function publicUrl(string $user_slug, UserRepository $userRepository)
    {
        $user= $userRepository->findOneBy(['user_slug' => $user_slug]);

        return $this->render("profile/public.html.twig", [
            'user' => $user
        ]);
    }
}
