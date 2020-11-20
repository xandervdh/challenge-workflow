<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Tickets;
use App\Form\CommentsType;
use App\Repository\CommentsRepository;
use App\Repository\TicketsRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\User;

/**
 * @Route("/comments")
 */
class CommentsController extends AbstractController
{
    private $session;
    private $repository;
    private $ticket;
    private $verified;

    /**
     * CommentsController constructor.
     * @param $session
     */
    public function __construct(SessionInterface $session, UsersRepository $repo, TicketsRepository $ticket, VerifyController $verified)
    {
        $this->session = $session;
        $this->repository = $repo;
        $this->ticket = $ticket;
        $this->verified = $verified;
    }


    /**
     * @Route("/", name="comments_index", methods={"GET"})
     */
    public function index(CommentsRepository $commentsRepository): Response
    {
        /*if( !$this->verified->checkVerified()){
            return $this->redirectToRoute('verify');
        }*/

        return $this->render('comments/index.html.twig', [
            'comments' => $commentsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{id}", name="comments_new", methods={"GET","POST"})
     */
    public function new(Request $request, $id): Response
    {
        $comment = new Comments();
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userId = $this->session->getId();
            $user = $this->repository->findOneById($userId);
            $ticket = $this->ticket->findOneBy(['id' => $id]);
            $comment->setUserId($user);
            $comment->setTicketId($ticket);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('tickets_show', ['id' => $id]);
        }

        return $this->render('comments/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="comments_show", methods={"GET"})
     */
    public function show(Comments $comment): Response
    {
        /*if( !$this->verified->checkVerified()){
            return $this->redirectToRoute('verify');
        }*/
        return $this->render('comments/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="comments_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Comments $comment): Response
    {

        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comments_index');
        }

        return $this->render('comments/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="comments_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Comments $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('comments_index');
    }
}
