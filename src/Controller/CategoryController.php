<?php
namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;

class CategoryController extends AbstractController
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
     * @Route("/category/{id}", name="category")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function category($id)
    {
        $category = $this->categoryRepository->find($id);
        $articles = $this->articleRepository->findBy(['category' => $id], ['date' => 'DESC']);

        return $this->render('category/category.html.twig', [
            'controller_name' => 'CategoryController',
            'articles' => $articles,
            'category' => $category,
            'featured' => $this->featured,
            'categories' => $this->categories
        ]);
    }

    /**
     * @Route("/admin/categories", name="adminCategories")
     */
    public function categories()
    {
        return $this->render('admin/category/categories.html.twig', [
            'categories' => $this->categories
        ]);
    }

    /**
     * @Route("/admin/categories/new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newCategory(Request $request)
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('adminCategories');
        }

        return $this->render('admin/category/new.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/categories/edit/{id}")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request, $id)
    {
        $category = $this->categoryRepository->find($id);

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('adminCategories');
        }

        return $this->render('admin/category/new.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/categories/delete/{id}")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete($id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $category = $this->categoryRepository->find($id);

        if(count($category->getArticles()) > 0) {
            //category is not empty
        }

        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('adminCategories');
    }
}
