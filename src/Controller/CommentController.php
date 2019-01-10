<?php

namespace App\Controller;

use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    private $commentRepository;
    private $comments;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
        $this->comments = $this->commentRepository->findAll();
    }

    /**
     * @Route("/admin/comments", name="adminComments")
     */
    public function comments()
    {
        return $this->render('admin/comment/comments.html.twig', [
            'comments' => $this->comments
        ]);
    }

    /**
     * @Route("/admin/comments/edit/{id}")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit($id, Request $request)
    {
        $comment = $this->commentRepository->find($id);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('adminComments');
        }

        return $this->render('admin/comment/form.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/comments/delete/{id}")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete($id)
    {
        $comment = $this->commentRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirectToRoute('adminComments');
    }
}
