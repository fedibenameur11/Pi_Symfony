<?php

namespace App\Controller;

use App\Entity\Salle;
use App\Form\SalleType;
use App\Repository\SalleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\SearchFormType;

#[Route('/salle')]
class SalleController extends AbstractController
{

    #[Route('/', name: 'app_salle_index', methods: ['GET', 'POST'])]
    public function index(Request $request, SalleRepository $salleRepository): Response
    {
        $form = $this->createForm(SearchFormType::class, null);
        $form->handleRequest($request);

        $salles = null;
        if ($form->isSubmitted() && $form->isValid()) {
            
            $searchValue = $form->getData()['searchValue']; 
            $salles = $salleRepository->findByAnything($searchValue);
        } else {
            
            $salles = $salleRepository->findAll();
        }

        return $this->render('salle/index.html.twig', [
            'salles' => $salles,
            'form' => $form->createView(),
        ]);
    }
    //#[Route('/', name: 'app_salle_index', methods: ['GET'])]
    //public function index(SalleRepository $salleRepository): Response
    //{
      //  return $this->render('salle/index.html.twig', [
        //    'salles' => $salleRepository->findAll(),
        //]);
    //}

    #[Route('/new', name: 'app_salle_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SalleRepository $salleRepository): Response
    {
        $salle = new Salle();
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $salleRepository->save($salle, true);

            return $this->redirectToRoute('app_salle_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('salle/new.html.twig', [
            'salle' => $salle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_salle_show', methods: ['GET'])]
    public function show(Salle $salle): Response
    {
        return $this->render('salle/show.html.twig', [
            'salle' => $salle,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_salle_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Salle $salle, SalleRepository $salleRepository): Response
    {
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $salleRepository->save($salle, true);

            return $this->redirectToRoute('app_salle_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('salle/edit.html.twig', [
            'salle' => $salle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_salle_delete', methods: ['GET','POST'])]
   /* public function delete(Request $request, Salle $salle, SalleRepository $salleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$salle->getId(), $request->request->get('_token'))) {
            $salleRepository->remove($salle, true);
        }

        return $this->redirectToRoute('app_salle_index', [], Response::HTTP_SEE_OTHER);
    }*/

    public function delete(Request $request, Salle $salle)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($salle);
        $entityManager->flush();

        return $this->redirectToRoute('app_salle_index');
    }
}
