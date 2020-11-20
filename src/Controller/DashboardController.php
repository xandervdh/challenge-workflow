<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use mysql_xdevapi\Exception;
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
    public function index()
    {

            /*if( !$this->verified->checkVerified()){
                return $this->redirectToRoute('verify');
            }*/

            return $this->render('dashboard/index.html.twig');
        }
//    /**
//     * @Route("/dashboard", name="dashboard")
//     */
    /*public function index(): Response
    {
        if ($this->isGranted('ROLE_MANAGER')) {

        } else if ($this->isGranted('ROLE_SECOND_LINE_AGENT')) {

        } else if ($this->isGranted('ROLE_AGENT')) {

        } else if ($this->isGranted('ROLE_CUSTOMER')) {

        }
        return $this->render('tickets/index.html.twig', [

        ]);
    }


    private function indexManager(): Response
    {
        return $this->render('tickets/index.html.twig', [

        ]);
    }

    private function indexSecondAgent(): Response
    {
        return $this->render('tickets/index.html.twig', [

        ]);
    }

    private function indexAgent(): Response
    {
        return $this->render('tickets/index.html.twig', [

        ]);
    }

    private function indexCustomer(): Response
    {
        return $this->render('tickets/index.html.twig', [

        ]);
    }*/
}
