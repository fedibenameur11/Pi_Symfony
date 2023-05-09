<?php

namespace App\Controller;

use App\Entity\Livraisons;
use App\Form\LivraisonsType;
use App\Repository\LivraisonsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/livraisons')]
class LivraisonsController extends AbstractController
{
    #[Route('/', name: 'app_livraisons_index', methods: ['GET'])]
    public function index(Request $request,LivraisonsRepository $livraisonsRepository,PaginatorInterface $paginator): Response
    {
        $livraisonsRepository = $this->getDoctrine()->getRepository(Livraisons::class)->findAll();
        $livraisonsRepository = $paginator->paginate(
            $livraisonsRepository,
            $request->query->getInt('page',1),
            4
        );
        return $this->render('livraisons/index.html.twig', [
            'livraisons' => $livraisonsRepository
        ]);

    }

    #[Route('/new', name: 'app_livraisons_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LivraisonsRepository $livraisonsRepository): Response
    {
        $livraison = new Livraisons();
        $form = $this->createForm(LivraisonsType::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livraisonsRepository->save($livraison, true);
            $this->addFlash(
                'info',
                'Element ajouté avec succès'
            );

            return $this->redirectToRoute('app_livraisons_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livraisons/new.html.twig', [
            'livraison' => $livraison,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_livraisons_show', methods: ['GET'])]
    public function show(Livraisons $livraison): Response
    {
        return $this->render('livraisons/show.html.twig', [
            'livraison' => $livraison,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_livraisons_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livraisons $livraison, LivraisonsRepository $livraisonsRepository): Response
    {
        $form = $this->createForm(LivraisonsType::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livraisonsRepository->save($livraison, true);
            $this->addFlash(
                'info',
                'Element modifié avec succès'
            );

            return $this->redirectToRoute('app_livraisons_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livraisons/edit.html.twig', [
            'livraison' => $livraison,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_livraisons_delete', methods: ['POST'])]
    public function delete(Request $request, Livraisons $livraison, LivraisonsRepository $livraisonsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livraison->getId(), $request->request->get('_token'))) {
            $livraisonsRepository->remove($livraison, true);
            $this->addFlash(
                'info',
                'Element supprimé avec succès'
            );
        }

        return $this->redirectToRoute('app_livraisons_index', [], Response::HTTP_SEE_OTHER);
    }
}
