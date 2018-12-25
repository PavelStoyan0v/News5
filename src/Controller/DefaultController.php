<?php
/**
 * Created by PhpStorm.
 * User: pavelst
 * Date: 12/21/2018
 * Time: 3:54 PM
 */

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Category;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $categoryRepository->findAll();
        return $this->render("home.html.twig", ["categories" => $categories]);
    }

}