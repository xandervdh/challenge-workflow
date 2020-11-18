<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UsersType;
use App\Repository\UsersRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
/**
 *
 * @Route("/users")
 *
 */
class UsersController extends AbstractController
{
    private $resetPasswordHelper;
    private $name;
    private $session;
    private $usersRepository;

    /**
     * UsersController constructor.
     * @param $name
     */
    public function __construct(SessionInterface $session, UsersRepository $repository)
    {
        $this->session = $session;
        $this->usersRepository = $repository;
        $email = $this->session->get('_security.last_username');
        $user = $this->usersRepository->findOneByEmail($email);
        $this->name = $user->getFirstName();
    }


    /**
     * @Route("/", name="users_index", methods={"GET"})
     */
    public function index(UsersRepository $usersRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');
        return $this->render('users/index.html.twig', [
            'users' => $usersRepository->findAll(),
            'name' => $this->name,
        ]);
    }


    /**
     * @Route("/new", name="users_new", methods={"GET","POST"})
     */
    public function new(Request $request ,UserPasswordEncoderInterface $passwordEncoder, MailerInterface $mailer): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');
        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $random = random_bytes(10)
                ));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        /*
            $email = (new TemplatedEmail())
                ->from(new Address('xandervanderherten@gmail.com', 'tomato bot'))
                ->to($user->getEmail())
                ->subject('Your password reset request')
                ->htmlTemplate('reset_password/email.html.twig')
                ->context([
                    'resetToken' => $resetToken,
                    'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
                ])
                

                $mailer->send($email);
            ;
        */
            return $this->redirectToRoute('users_index');
        }

        return $this->render('users/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);



    }

    /**
     * @Route("/{id}", name="users_show", methods={"GET"})
     */
    public function show(Users $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');
        return $this->render('users/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="users_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Users $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('users_index');
        }

        return $this->render('users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="users_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Users $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('users_index');
    }
}
