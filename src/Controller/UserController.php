<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\CategorieUser;
use App\Form\ClientType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategorieUserRepository;



#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/new/client', name: 'app_user_newc', methods: ['GET', 'POST'])]
    public function newClient(Request $request, UserRepository $userRepository,CategorieUserRepository $categorieUserRepository): Response
    {
        $user = new User();
        $CategorieUser = new CategorieUser();
        $CategorieUser->setNom("client") ;
        $user->setCategorieUser($CategorieUser);

        $form = $this->createForm(ClientType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorieUserRepository->save($CategorieUser, true);
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index1', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('basefrom.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    #[Route('/index/1', name: 'app_user_index1', methods: ['GET'])]
    public function indexFront(UserRepository $userRepository): Response
    {
        return $this->render('/base1.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
}
