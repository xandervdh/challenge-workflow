<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\EmailVerifier;
class VerifyController extends AbstractController
{
    private  $session;
    private $usersRepository;
    private $newLink;

    /**
     * VerifyController constructor.
     * @param $session
     * @param $usersRepository
     */
    public function __construct(SessionInterface $session, UsersRepository $usersRepository, EmailVerifier $newLink)
    {
        $this->newLink = $newLink;
        $this->session = $session;
        $this->usersRepository = $usersRepository;
    }


    /**
     * @Route("/verify", name="verify")
     */
    public function index(): Response
    {
        $this->newLink->sendEmailConfirmation();
        return $this->render('verify/index.html.twig', [
            'controller_name' => 'VerifyController',
        ]);
    }

    public function checkVerified()
    {
        $email = $this->session->get('_security.last_username');
        $user = $this->usersRepository->findOneByEmail($email);
        return $user->isVerified();

    }

}


