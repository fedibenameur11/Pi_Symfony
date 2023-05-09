<?php

namespace App\Controller;

use App\Entity\AbonnementSalle;
use App\Form\AbonnementSalleType;
use App\Repository\AbonnementSalleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/abonnement/salle3')]
class abonnementsalle3Controller extends AbstractController
{
    #[Route('/', name: 'app_abonnement_salle_index3', methods: ['GET'])]
    public function index(AbonnementSalleRepository $abonnementSalleRepository): Response
    {
        return $this->render('abonnement_salle/index3.html.twig', [
            'abonnement_salles' => $abonnementSalleRepository->findAll(),
        ]);
    } 
}

    
    

   

   

    

    
