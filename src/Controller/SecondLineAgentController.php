<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecondLineAgentController extends AbstractController
{
    /**
     * @Route("/se/second/line/agent", name="second_line_agent")
     */
    public function index(): Response
    {
        return $this->render('second_line_agent/index.html.twig', [
            'controller_name' => 'SecondLineAgentController',
        ]);
    }
}
