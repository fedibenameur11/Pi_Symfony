<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Test2Controller extends AbstractController
{
    #[Route('/test2', name: 'app_test2')]
    public function index(): Response
    {
        return $this->render('base2.html.twig', [
            'controller_name' => 'Test2Controller',
        ]);
    }
}
