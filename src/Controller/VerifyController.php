<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\EmailVerifier;
class VerifyController extends AbstractController
{
    private $session;
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
        $user = $this->getUser();
        $this->newLink->sendEmailConfirmation("app_login", $user,
            (new TemplatedEmail())
                ->from(new Address('xandervanderherten@gmail.com', 'tomato bot'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        return $this->render('verify/index.html.twig', [
            'controller_name' => 'VerifyController',
        ]);
    }

    public function checkVerified()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        return $user->isVerified();
    }

}


