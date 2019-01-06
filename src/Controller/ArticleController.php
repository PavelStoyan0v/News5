<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

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

        return $this->render('admin/article/articles.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/admin/articles/edit/{id}")
     */
    public function edit($id, Request $request, UserInterface $user)
    {
        $articleRepository = $this->getDoctrine()->getRepository(Article::class);
        $article = $articleRepository->find($id);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('image')->getData();

            if($file) {
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

                try {
                    $file->move($this->getParameter('image_directory'), $fileName);
                } catch (FileException $e) {
                    // TODO: handle exception
                }
                $article->setImage($fileName);
            }

            $article->setDate(new DateTime());


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('adminArticles');
        }

        return $this->render('admin/article/article-new.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/articles/delete/{id}")
     */
    public function delete($id)
    {
        $articleRepository = $this->getDoctrine()->getRepository(Article::class);
        $article = $articleRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('adminArticles');
    }

    /**
     * @Route("/admin/articles/new")
     */
    public function new(Request $request, UserInterface $user)
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('image')->getData();

            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

            try {
                $file->move($this->getParameter('image_directory'), $fileName);
            } catch (FileException $e) {
                // TODO: handle exception
            }

            $article->setImage($fileName);
            $article->setDate(new DateTime());
            $article->setAuthor($user);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('adminArticles');
        }

        return $this->render('admin/article/article-new.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/articles/publish", name="adminPublish")
     */
    public function publish()
    {
        $articleRepository = $this->getDoctrine()->getRepository(Article::class);
        $articles = $articleRepository->findBy(['published' => false], ['date' => 'DESC']);

        return $this->render('admin/article/publish.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/admin/articles/publish/{id}")
     */
    public function publishArticle($id) {
        $entityManager = $this->getDoctrine()->getManager();
        $articleRepository = $this->getDoctrine()->getRepository(Article::class);
        $article = $articleRepository->find($id);

        $article->setPublished(true);

        $entityManager->persist($article);
        $entityManager->flush();

        return $this->redirectToRoute('adminPublish');
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}
