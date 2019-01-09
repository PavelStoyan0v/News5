<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/admin/users", name="adminUsers")
     */
    public function users()
    {
        $users = $this->userRepository->findAll();

        return $this->render('admin/user/users.html.twig', [
            'users' => $users
        ]);
    }
}
