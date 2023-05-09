<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommandesRepository;
use App\Repository\LivraisonsRepository;
use App\Entity\Commandes;
use App\Form\CommandesType;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('base1.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/comm', name: 'comm_test')]
    public function index1(CommandesRepository $commandesRepository): Response
    {
        return $this->render('commandes/affiche.html.twig', [
            'commandes' => $commandesRepository->findAll()
        ]);
    }
    #[Route('/liv', name: 'liv_test')]
    public function index2(LivraisonsRepository $livraisonsRepository): Response
    {
        return $this->render('livraisons/affiche.html.twig', [
            'livraison' => $livraisonsRepository->findAll()
        ]);
    }

}
