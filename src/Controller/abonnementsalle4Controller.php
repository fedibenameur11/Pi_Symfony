<?php

namespace App\Controller;

use App\Entity\AbonnementSalle;
use App\Form\AbonnementSalleType;
use App\Repository\AbonnementSalleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/abonnement/salle4')]
class abonnementsalle4Controller extends AbstractController
{
    

    #[Route('/new1', name: 'app_abonnement_salle_new1', methods: ['GET', 'POST'])]
    public function new(Request $request, AbonnementSalleRepository $abonnementSalleRepository): Response
    {
        $abonnementSalle = new AbonnementSalle();
        $form = $this->createForm(AbonnementSalleType::class, $abonnementSalle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $abonnementSalleRepository->save($abonnementSalle, true);

            return $this->redirectToRoute('app_abonnement_salle_new1', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('abonnement_salle/new1.html.twig', [
            'abonnement_salle' => $abonnementSalle,
            'form' => $form,
        ]);
    }

    

    

    

    
}
