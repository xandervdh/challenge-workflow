<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Tickets;
use App\Entity\Users;
use App\Form\StatusType;
use App\Form\TicketsType;
use App\Form\UpdateType;
use App\Repository\CommentsRepository;
use App\Repository\TicketsRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\VerifyController;

/**
 * @Route("/tickets")
 */
class TicketsController extends AbstractController
{
    private $session;
    private $usersRepository;
    private $name;
    private $verified;

    public function __construct(SessionInterface $session, UsersRepository $repository, VerifyController $verified)
    {
        $this->session = $session;
        $this->usersRepository = $repository;
        $this->verified = $verified;
    }

    /**
     *
     * @Route("/", name="tickets_index", methods={"GET"})
     */
    public function indexAction(TicketsRepository $ticketsRepository)
    {

        /*if( !$this->verified->checkVerified()){
            return $this->redirectToRoute('verify');
        }*/

        if ($this->isGranted('ROLE_MANAGER')) {
            return $this->indexManager($ticketsRepository);
        } else if ($this->isGranted('ROLE_SECOND_LINE_AGENT')) {
            return $this->indexSecondAgent($ticketsRepository);
        } else if ($this->isGranted('ROLE_AGENT')) {
            return $this->indexAgent($ticketsRepository);
        } else if ($this->isGranted('ROLE_CUSTOMER')) {
            return $this->indexCustomer();
        }
        return $this->render('tickets/index.html.twig', [
        'tickets' => 'you are not allowed here',
        ]);
    }


    private function indexManager(TicketsRepository $ticketsRepository): Response
    {
        return $this->render('tickets/index.html.twig', [
            'tickets' => $ticketsRepository->findAll(),
        ]);
    }

    private function indexSecondAgent(TicketsRepository $ticketsRepository): Response
    {
        return $this->render('tickets/index.html.twig', [
            'tickets' => $ticketsRepository->findByStatus('escalated'),
        ]);
    }

    private function indexAgent(TicketsRepository $ticketsRepository): Response
    {
        return $this->render('tickets/index.html.twig', [
            'tickets' => $ticketsRepository->findByStatus('open'),
        ]);
    }

    private function indexCustomer(): Response
    {
        $email = $this->session->get('_security.last_username');
        $user = $this->usersRepository->findOneByEmail($email);

        return $this->render('tickets/index.html.twig', [
            'tickets' => $user->getTickets(),
            'name' => $this->name,
        ]);
    }

    /**
     * @Route("/new", name="tickets_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        /*if( !$this->verified->checkVerified()){
            return $this->redirectToRoute('verify');
        }*/
        $ticket = new Tickets();
        $form = $this->createForm(TicketsType::class, $ticket);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $this->session->get('_security.last_username');
            $user = $this->usersRepository->findOneByEmail($email);
            $ticket->addCustomerId($user);
            $ticket->setStatus('open');
            $ticket->setDateTime(new \DateTime());
            $ticket->setPriority('0');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('tickets_index');
        }
        return $this->render('tickets/new.html.twig', [

            'form' => $form->createView(),
            'name' => $this->name
        ]);
    }

    /**
     * @Route("/{id}", name="tickets_show", methods={"GET"})
     */
    public function show(Tickets $ticket, CommentsRepository $commentsRepository, $id, Request $request): Response
    {
        /*if( !$this->verified->checkVerified()){
            return $this->redirectToRoute('verify');
        }*/
        $form = $this->createForm(StatusType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tickets_index');
        }

        return $this->render('tickets/show.html.twig', [
            'ticket' => $ticket,
            'comments' => $commentsRepository->findByTicketId($id),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tickets_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tickets $ticket): Response
    {
        /*if( !$this->verified->checkVerified()){
            return $this->redirectToRoute('verify');
        }*/
        $form = $this->createForm(TicketsType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tickets_index');
        }

        return $this->render('tickets/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tickets_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Tickets $ticket): Response
    {
        /*if( !$this->verified->checkVerified()){
            return $this->redirectToRoute('verify');
        }*/
        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tickets_index');
    }

    /**
     * @Route("/agents/list", name="tickets_agent")
     */
    public function agentTickets(UsersRepository $repo, TicketsRepository $tRepo): Response
    {
        /*if( !$this->verified->checkVerified()){
            return $this->redirectToRoute('verify');
        }*/

        $email = $this->getUser()->getUsername();
        $user = $repo->findOneByEmail($email);
        $id = $user->getId();
        $tickets = $tRepo->findByAssignedId($id);

        return $this->render('tickets/agent.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    /**
     * @Route("/update/{id}", name="tickets_update", methods={"GET"})
     */
    public function claim($id, UsersRepository $repo): Response
    {
        /*if( !$this->verified->checkVerified()){
            return $this->redirectToRoute('verify');
        }*/
        $entityManager = $this->getDoctrine()->getManager();
        $ticket = $entityManager->getRepository(Tickets::class)->find($id);
        $email = $this->getUser()->getUsername();
        $user = $repo->findOneByEmail($email);
        $ticket->setPriority('1');
        $ticket->setAssignedTo($user);

        $entityManager->flush();

        return $this->redirectToRoute('tickets_index');
    }

    /**
     * @Route("/all/reset", name="tickets_reset", methods={"GET"})
     */
    public function resetTicket(TicketsRepository $repo): Response
    {
        $tickets = $repo->findBy(['status' => ['in progress', 'escalated', 'require more information']]);
        $entityManager = $this->getDoctrine()->getManager();
        foreach ($tickets as $ticket){

            $ticket = $entityManager->getRepository(Tickets::class)->find($ticket->getId());
            $ticket->setStatus('open');
            $ticket->setAssignedTo(null);


        }
        $entityManager->flush();

        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/priority/{id}", name="tickets_priority", methods={"GET"})
     */
    public function setPriority($id, UsersRepository $repo): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $ticket = $entityManager->getRepository(Tickets::class)->find($id);
        $ticket->setPriority(1);

        $entityManager->flush();

        return $this->redirectToRoute('tickets_index');
    }

    /**
     * @Route("/nopriority/{id}", name="tickets_no_priority", methods={"GET"})
     */
    public function removePriority($id, UsersRepository $repo): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $ticket = $entityManager->getRepository(Tickets::class)->find($id);
        $ticket->setPriority(0);

        $entityManager->flush();

        return $this->redirectToRoute('tickets_index');
    }
}
