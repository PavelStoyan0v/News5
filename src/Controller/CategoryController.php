<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category/{id}", name="category")
     */
    public function category($id)
    {
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $articleRepository = $this->getDoctrine()->getRepository(Article::class);
        $categories = $categoryRepository->findAll();
        $category = $categoryRepository->find($id);
        $articles = $articleRepository->findBy(['category' => $id], ['date' => 'DESC']);

        $featured = $articleRepository->findBy(['featured' => true], ['date' => 'DESC'], 4 /* featured per page TODO: make it into a config file */);

        return $this->render('category/category.html.twig', [
            'controller_name' => 'CategoryController',
            'articles' => $articles,
            'category' => $category,
            'featured' => $featured,
            'categories' => $categories
        ]);
    }
}
