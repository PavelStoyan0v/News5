<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\CategoryRepository;
use App\Repository\ArticleRepository;

class DefaultController extends AbstractController
{
    private $articleRepository;
    private $categoryRepository;
    private $categories;
    private $articles;
    private $featured;

    public function __construct(ArticleRepository $articleRepository, CategoryRepository $categoryRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->categoryRepository = $categoryRepository;

        $this->articles = $this->articleRepository->findBy([],['date' => 'DESC']);
        $this->categories = $this->categoryRepository->findAll();
        $this->featured = $this->articleRepository->findBy(['featured' => true], ['date' => 'DESC'], 4);
    }

    /**
     * @Route("/", name="homepage")
     */
    public function home()
    {
        return $this->render('home.html.twig', ['categories' => $this->categories, 'articles' => $this->articles, 'featured' => $this->featured]);
    }
}