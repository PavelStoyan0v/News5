<?php
namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleController extends AbstractController
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
     * @Route("/article/{id}", name="article")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function article($id)
    {
        $article = $this->articleRepository->find($id);

        return $this->render('article/article.html.twig', [
            'controller_name' => 'ArticleController',
            'article' => $article,
            'categories' => $this->categories,
            'featured' => $this->featured
        ]);
    }

    /**
     * @Route("/admin/articles", name="adminArticles")
     */
    public function adminArticles()
    {
        $articles = $this->articleRepository->findAll();

        return $this->render('admin/article/articles.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/admin/articles/edit/{id}")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit($id, Request $request)
    {
        $article = $this->articleRepository->find($id);
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('image')->getData();

            if($file) {
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                $file->move($this->getParameter('image_directory'), $fileName);
                $article->setImage($fileName);
            }

            $article->setDate(new DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('adminArticles');
        }

        return $this->render('admin/article/edit.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/articles/delete/{id}")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete($id)
    {
        $article = $this->articleRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('adminArticles');
    }

    /**
     * @Route("/admin/articles/new")
     * @param Request $request
     * @param UserInterface $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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

        return $this->render('admin/article/new.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/articles/publish", name="adminPublish")
     */
    public function publish()
    {
        $articles = $this->articleRepository->findBy(['published' => false], ['date' => 'DESC']);

        return $this->render('admin/article/publish.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/admin/articles/publish/{id}")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function publishArticle($id) {
        $entityManager = $this->getDoctrine()->getManager();
        $article = $this->articleRepository->find($id);

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
