<?php
/**
 * AdminDefaultController class.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminDefaultController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_homepage")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig');
    }
}
