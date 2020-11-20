<?php

namespace App\Controller;

use App\Repository\TicketsRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    private $session;
    private $usersRepository;
    private $verified;

    /**
     * DashboardController constructor.
     * @param $session
     */
    public function __construct(SessionInterface $session, UsersRepository $repository, VerifyController $verified)
    {
        $this->session = $session;
        $this->usersRepository = $repository;
        $this->verified = $verified;
    }


    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(TicketsRepository $repo)
    {

        /*if( !$this->verified->checkVerified()){
            return $this->redirectToRoute('verify');
        }*/
        $openTickets = $repo->findByStatus('open');
        $closedTickets = $repo->findByStatus('closed');
        $reopenedTickets = $repo->findByStatus('reopened');
        $allTickets = count($repo->findAll());
        $amountOpen = count($openTickets);
        $amountClosed = count($closedTickets);
        $amountReopened = count($reopenedTickets);

        $percentage = ($amountReopened / $allTickets) * 100;

        return $this->render('dashboard/index.html.twig', [
            'open' => $amountOpen,
            'closed' => $amountClosed,
            'reopend' => $amountReopened,
            'percentage' => $percentage,
        ]);
    }

}
