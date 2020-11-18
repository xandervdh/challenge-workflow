<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class VerifyController extends AbstractController
{
    private  $session;
    private $usersRepository;

    /**
     * VerifyController constructor.
     * @param $session
     * @param $usersRepository
     */
    public function __construct(SessionInterface $session, UsersRepository $usersRepository)
    {
        $this->session = $session;
        $this->usersRepository = $usersRepository;
    }


    /**
     * @Route("/verify", name="verify")
     */
    public function index(): Response
    {
        return $this->render('verify/index.html.twig', [
            'controller_name' => 'VerifyController',
        ]);
    }

    //THIS FUNCTION DOESN'T WORK YET
    public function checkVerified()
    {
        $email = $this->session->get('_security.last_username');
        $user = $this->usersRepository->findOneByEmail($email);
       return $user->isVerified();

    }

}
