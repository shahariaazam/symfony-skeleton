<?php
/**
 * AdminDefaultController class.
 */

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminDefaultController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_homepage")
     */
    public function index(UserRepository $userRepository)
    {
        $totalUsers = $userRepository->count([]);

        return $this->render('admin/index.html.twig', [
            'totalUsers' => $totalUsers,
        ]);
    }
}
