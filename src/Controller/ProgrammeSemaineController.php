<?php

namespace App\Controller;

use App\Entity\ProgrammeSemaine;
use App\Entity\Programme;
use App\Form\ProgrammeSemaineType;
use App\Repository\ProgrammeSemaineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/programme_semaine')]
class ProgrammeSemaineController extends AbstractController
{
    #[Route('/', name: 'app_programme_semaine_index', methods: ['GET'])]
    public function index(ProgrammeSemaineRepository $programmeSemaineRepository): Response
    {
        return $this->render('programme_semaine/index.html.twig', [
            'programme_semaines' => $programmeSemaineRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_programme_semaine_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProgrammeSemaineRepository $programmeSemaineRepository): Response
    {
        $programmeSemaine = new ProgrammeSemaine();
        $form = $this->createForm(ProgrammeSemaineType::class, $programmeSemaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $programmeSemaineRepository->save($programmeSemaine, true);

            return $this->redirectToRoute('app_programme_semaine_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('programme_semaine/new.html.twig', [
            'programme_semaine' => $programmeSemaine,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_programme_semaine_show', methods: ['GET'])]
    public function show(ProgrammeSemaine $programmeSemaine): Response
    {
        return $this->render('programme_semaine/show.html.twig', [
            'programme_semaine' => $programmeSemaine,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_programme_semaine_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProgrammeSemaine $programmeSemaine, ProgrammeSemaineRepository $programmeSemaineRepository): Response
    {
        $form = $this->createForm(ProgrammeSemaineType::class, $programmeSemaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $programme = $this->getDoctrine()
                ->getRepository(Programme::class)
                ->find($programmeSemaine->getProgramme()->getId());
                dump($programme);
            $programmeSemaine->setProgramme($programme);
            $programmeSemaineRepository->save($programmeSemaine, true);

            return $this->redirectToRoute('app_abonnement_coach_programme_infos', ['id' => $programmeSemaine->getProgramme()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('programme_semaine/edit.html.twig', [
            'programme_semaine' => $programmeSemaine,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_programme_semaine_delete', methods: ['POST'])]
    public function delete(Request $request, ProgrammeSemaine $programmeSemaine, ProgrammeSemaineRepository $programmeSemaineRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$programmeSemaine->getId(), $request->request->get('_token'))) {
            $programmeSemaineRepository->remove($programmeSemaine, true);
        }

        return $this->redirectToRoute('app_programme_semaine_index', [], Response::HTTP_SEE_OTHER);
    }
}
