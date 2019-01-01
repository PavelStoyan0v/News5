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
use App\Entity\Article;

class DefaultController extends AbstractController
{
    private $featuredPerPage = 4;
    /**
     * @Route("/", name="homepage")
     */
    public function home()
    {
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        $articleRepository = $this->getDoctrine()->getRepository(Article::class);
        $articles = $articleRepository->findBy([],['date' => 'DESC']);
        $featured = $articleRepository->findBy(['featured' => true], ['date' => 'DESC'], $this->featuredPerPage);

        return $this->render('home.html.twig', ['categories' => $categories, 'articles' => $articles, 'featured' => $featured]);
    }
}