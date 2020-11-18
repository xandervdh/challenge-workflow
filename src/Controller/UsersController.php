<?php

namespace App\Controller;

use App\Entity\ResetPasswordRequest;
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
use App\Controller\VerifyController;

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
    private $verified;

    /**
     * UsersController constructor.
     * @param $name
     */
    public function __construct(SessionInterface $session, UsersRepository $repository, VerifyController $verified)
    {
        $this->session = $session;
        $this->usersRepository = $repository;
        $email = $this->session->get('_security.last_username');
        $user = $this->usersRepository->findOneByEmail($email);
        $this->name = $user->getFirstName();
        $this->verified = $verified;
    }


    /**
     * @Route("/", name="users_index", methods={"GET"})
     */
    public function index(UsersRepository $usersRepository): Response
    {
        if( !$this->verified->checkVerified()){
            return $this->redirectToRoute('verify');
        }
        $this->denyAccessUnlessGranted('ROLE_MANAGER');
        return $this->render('users/index.html.twig', [
            'users' => $usersRepository->findAll(),
            'name' => $this->name,
        ]);
    }


    /**
     * @Route("/new", name="users_new", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param MailerInterface $mailer
     * @param $expiresAt
     * @param $selector
     * @param $hashedtoken
     * @return Response
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder, MailerInterface $mailer): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');
        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $randomBit = $this->readable_random_string();

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                   $randomBit
                ));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();



            $email = (new TemplatedEmail())
                ->from(new Address('xandervanderherten@gmail.com', 'tomato bot'))
                ->to($user->getEmail())
                ->subject('Your password reset request')
                ->htmlTemplate('reset_password/emailAgent.html.twig')
                ->context([
                    'randomBit' => $randomBit,
                ])
                ;

            $mailer->send($email);


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
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('users_index');
    }

    function readable_random_string($length = 6)
    {
        $string = '';
        $vowels = array("a","e","i","o","u");
        $consonants = array(
            'b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm',
            'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z'
        );

        $max = $length / 2;
        for ($i = 1; $i <= $max; $i++)
        {
            $string .= $consonants[rand(0,19)];
            $string .= $vowels[rand(0,4)];
        }

        return $string;
    }
}
