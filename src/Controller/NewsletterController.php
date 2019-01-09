<?php
namespace App\Controller;

use App\Entity\NewsletterEntry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

class NewsletterController extends AbstractController
{
    /**
     * @Route("/newsletter", name="newsletter")
     * @param Request $request
     * @param LoggerInterface $logger
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function index(Request $request, LoggerInterface $logger)
    {
        if($request->getMethod() == 'POST') {
            try {
                $em = $this->getDoctrine()->getManager();
                $email = $request->request->get('email');
                $newsletterEntry = new NewsletterEntry($email);

                $em->persist($newsletterEntry);
                $em->flush();

                return new Response('ok');
            } catch(\Exception $e) {
                $logger->error($e->getMessage());
                return new Response('Something went wrong, try again later!');
            }
        }

        return $this->redirectToRoute('homepage');
    }
}
