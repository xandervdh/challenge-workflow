<?php

namespace App\Controller;

use App\Entity\Tickets;
use App\Entity\Users;
use App\Form\TicketsType;
use App\Repository\TicketsRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\User;

/**
 * @Route("/tickets")
 */
class TicketsController extends AbstractController
{
    private $session;
    private $usersRepository;

    public function __construct(SessionInterface $session, UsersRepository $repository)
    {
        $this->session = $session;
        $this->usersRepository = $repository;
    }


//THIS FUNCTION DOESN'T WORK YET
    public function checkVerified()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $verified = $entityManager->getRepository(Users::class)->findOneBy(['is_verified' => 1 ]);
        if ($this->getUser() != $verified){
        echo "Your email has not yet been verified";
            return $this->redirectToRoute('app_login');
        }
    }



    /**
     *
     * @Route("/", name="tickets_index", methods={"GET"})
     */
    public function indexAction(TicketsRepository $ticketsRepository)
    {
        if ($this->isGranted('ROLE_MANAGER')) {
            return $this->indexManager($ticketsRepository);
        } else if ($this->isGranted('ROLE_SECOND_LINE_AGENT')) {
            return $this->indexSecondAgent($ticketsRepository);
        } else if ($this->isGranted('ROLE_AGENT')) {
            return $this->indexAgent($ticketsRepository);
        } else if ($this->isGranted('ROLE_CUSTOMER')) {
            return $this->indexCustomer($ticketsRepository);
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

    private function indexCustomer(TicketsRepository $ticketsRepository): Response
    {
        return $this->render('tickets/index.html.twig', [
            'tickets' => $ticketsRepository->findByStatus('closed'),
            'succes' => 'sucesses my man',
        ]);
    }

    /**
     * @Route("/new", name="tickets_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
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
        $message = $this->session->get('_security.last_username');
        echo $message;
        return $this->render('tickets/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tickets_show", methods={"GET"})
     */
    public function show(Tickets $ticket): Response
    {
        return $this->render('tickets/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tickets_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tickets $ticket): Response
    {
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
        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tickets_index');
    }
}
