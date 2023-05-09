<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Users;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;
use App\Repository\ReponseRepository;
use App\Repository\LikeReponseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Doctrine\ORM\EntityManagerInterface;


#[Route('/question')]
class QuestionController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/{id}/delete', name: 'app_question_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Question $question, ReponseRepository $reponseRepository)
    {
        
        $this->entityManager->remove($question);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_question_index');
    }

     #[Route('/widget', name: 'app_widget', methods: ['GET'])]
    public function widget(QuestionRepository $questionRepository,ReponseRepository $reponseRepository,LikeReponseRepository $likeReponseRepository,UserRepository $userRepository): Response
    {
        return $this->render('question/widget.html.twig', [
            'question' => $questionRepository->findAll(),
            'reponse' => $reponseRepository->findAll(),
            'likeReponse' => $likeReponseRepository->findAll(),
            'userRepository' => $userRepository->findAll(),

        ]);
    }


    #[Route('/', name: 'app_question_index', methods: ['GET'])]
    public function index(QuestionRepository $questionRepository): Response
    {
        return $this->render('question/index.html.twig', [
            'question' => $questionRepository->findAll(),

        ]);
    }

    //json
    #[Route('/json', name: 'app_question_index_json', methods: ['GET'])]
    public function getQuestion(QuestionRepository $questionRepository, NormalizerInterface $normalizer): Response
    {
        $question = $questionRepository->findAll();
        $questionNormalises = $normalizer->normalize($question, 'json', ['groups' => "question"]);
        $json = json_encode($questionNormalises);
        return new Response($json);
    }


    #[Route('/new', name: 'app_question_new', methods: ['GET', 'POST'])]
    public function new(Request $request, QuestionsRepository $questionRepository): Response
    {
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $questionRepository->save($question, true);
            $createdAt= $question->getcreatedAt();
            $dateCreationString = $question->getDateCreation()->format('Y-m-d H:i:s');
            $question->setcreatedAt(new \datetime);

            return $this->redirectToRoute('app_question_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('questions/new.html.twig', [
            'question' => $question,
            'form'=>$form,
        ]);
    }

    #[Route('/show/{id}', name: 'app_question_show', methods: ['GET'])]
    public function show(Question $question): Response
    {
        return $this->render('question/show.html.twig', [

            'question' => $question,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_question_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Question $question, QuestionRepository $questionRepository): Response
    {
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $questionRepository->save($question, true);

            return $this->redirectToRoute('app_question_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('question/edit.html.twig', [
            'question' => $question,
            'form' => $form,
        ]);
    }


    #[Route('/new1', name: 'app_question_news', methods: ['GET', 'POST'])]
    public function new1(Request $request, QuestionRepository $questionRepository): Response
    {


       // dd($questionRepository->findAll());
        $question = new Question();

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $question->setUser($this->getUser());
            $questionRepository->save($question, true);

            return $this->redirectToRoute('app_question_news', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('question/new1.html.twig', [
            'question' => $question,
            'form' => $form,
            'questions' => $questionRepository->findAll(), 
        ]);
    }

}



