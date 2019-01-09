<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function home()
    {
        return $this->render('admin/home.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
