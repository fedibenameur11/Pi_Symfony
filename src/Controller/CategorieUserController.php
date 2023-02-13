<?php

namespace App\Controller;

use App\Entity\CategorieUser;
use App\Form\CategorieUserType;
use App\Repository\CategorieUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categorie_user')]
class CategorieUserController extends AbstractController
{
    #[Route('/', name: 'app_categorie_user_index', methods: ['GET'])]
    public function index(CategorieUserRepository $categorieUserRepository): Response
    {
        return $this->render('categorie_user/index.html.twig', [
            'categorie_users' => $categorieUserRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_categorie_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategorieUserRepository $categorieUserRepository): Response
    {
        $categorieUser = new CategorieUser();
        $form = $this->createForm(CategorieUserType::class, $categorieUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorieUserRepository->save($categorieUser, true);

            return $this->redirectToRoute('app_categorie_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorie_user/new.html.twig', [
            'categorie_user' => $categorieUser,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categorie_user_show', methods: ['GET'])]
    public function show(CategorieUser $categorieUser): Response
    {
        return $this->render('categorie_user/show.html.twig', [
            'categorie_user' => $categorieUser,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categorie_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategorieUser $categorieUser, CategorieUserRepository $categorieUserRepository): Response
    {
        $form = $this->createForm(CategorieUserType::class, $categorieUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorieUserRepository->save($categorieUser, true);

            return $this->redirectToRoute('app_categorie_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorie_user/edit.html.twig', [
            'categorie_user' => $categorieUser,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categorie_user_delete', methods: ['POST'])]
    public function delete(Request $request, CategorieUser $categorieUser, CategorieUserRepository $categorieUserRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorieUser->getId(), $request->request->get('_token'))) {
            $categorieUserRepository->remove($categorieUser, true);
        }

        return $this->redirectToRoute('app_categorie_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
