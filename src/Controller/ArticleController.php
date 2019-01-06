<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article/{id}", name="article")
     */
    public function article($id)
    {
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        $articleRepository = $this->getDoctrine()->getRepository(Article::class);
        $article = $articleRepository->find($id);
        $featured = $articleRepository->findBy(['featured' => true], ['date' => 'DESC'], 4 /* featured per page TODO: make it into a config file */);

        return $this->render('article/article.html.twig', [
            'controller_name' => 'ArticleController',
            'article' => $article,
            'categories' => $categories,
            'featured' => $featured
        ]);
    }

    /**
     * @Route("/admin/articles", name="adminArticles")
     */
    public function adminArticles()
    {
        $articleRepository = $this->getDoctrine()->getRepository(Article::class);
        $articles = $articleRepository->findAll();

        return $this->render('admin/articles.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/admin/articles/edit/{id}")
     */
    public function editArticle($id)
    {
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $articleRepository = $this->getDoctrine()->getRepository(Article::class);
        $article = $articleRepository->find($id);
        $categories = $categoryRepository->findAll();

        return $this->render('admin/article-edit.html.twig', [
            'categories' => $categories,
            'article' => $article
        ]);
    }

    /**
     * @Route("/admin/articles/new")
     */
    public function newArticle()
    {
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        return $this->render('admin/article-new.html.twig',[
            'categories' => $categories
        ]);
    }
}
