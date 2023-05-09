<?php

namespace App\Controller;

use App\Entity\Salle;
use App\Form\SalleType;
use App\Repository\SalleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/salle2')]
class salle2Controller extends AbstractController
{
    #[Route('/', name: 'app_salle_index2', methods: ['GET'])]
    public function index(SalleRepository $salleRepository): Response
    {
        return $this->render('salle/index2.html.twig', [
            'salles' => $salleRepository->findAll(),
        ]);
    }

    #[Route('/json', name: 'app_salle_index2_json', methods: ['GET'])]
    public function getSalles(SalleRepository $salleRepository, NormalizerInterface $normalizer): Response
    {
        $salle=$salleRepository->findAll();
        $salleNormalises=$normalizer->normalize($salle, 'json', ['groups'=>"salle"]);
        $json=json_encode($salleNormalises);
        return new Response($json);
    }

    
    #[Route('/{id}', name: 'app_salle_show', methods: ['GET'])]
    public function show(Salle $salle): Response
    {
        return $this->render('salle/show.html.twig', [
            'salle' => $salle,
        ]);
    }
}