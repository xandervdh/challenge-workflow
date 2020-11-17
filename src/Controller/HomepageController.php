<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    private $session;
    private $usersRepository;

    /**
     * HomepageController constructor.
     * @param $session
     */
    public function __construct(SessionInterface $session, UsersRepository $repository)
    {
        $this->session = $session;
        $this->usersRepository = $repository;
    }


    /**
    * @Route("/", name="homepage")
    */
    public function index()
    {
        $email = $this->session->get('_security.last_username');
        $user = $this->usersRepository->findOneByEmail($email);
        return $this->render('homepage/index.html.twig', [
            'name' => $user->getFirstName(),
        ]);
    }
//    /**
//     * @Route("/homepage", name="homepage")
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
