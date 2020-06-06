<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function index(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        return $this->render('users/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/users/{uuid}", name="admin_user_details")
     */
    public function details(string $uuid, UserRepository $userRepository)
    {
        $user = $userRepository->findOneBy(['uuid' => $uuid]);
        if (empty($user)) {
            throw $this->createNotFoundException('User not found');
        }

        return $this->render('users/details.html.twig', [
            'user' => $user,
        ]);
    }
}
