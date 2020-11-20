<?php

namespace App\Controller;

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
    public function index()
    {

            /*if( !$this->verified->checkVerified()){
                return $this->redirectToRoute('verify');
            }*/

            return $this->render('dashboard/index.html.twig');
        }

}
