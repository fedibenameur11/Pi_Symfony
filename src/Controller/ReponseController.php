<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Entity\LikeReponse;
use App\Entity\Question;
use App\Form\ReponseType;
use App\Repository\QuestionRepository;
use App\Repository\ReponseRepository;
use App\Repository\LikeReponseRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/reponse')]
class ReponseController extends AbstractController
{
 private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/', name: 'app_reponse_index', methods: ['GET'])]
    public function index(ReponseRepository $reponseRepository): Response
    {
        return $this->render('reponse/index.html.twig', [
            'reponse' => $reponseRepository->findAll(),
        ]);
    } 

    #[Route('/json1', name: 'app_reponse_index_json', methods: ['GET'])]
    public function getReponse(ReponseRepository $reponseRepository, NormalizerInterface $normalizer): Response
    {
        $reponse = $reponseRepository->findAll();
        $reponseNormalises = $normalizer->normalize($reponse, 'json', ['groups' => "question"]);
        $json = json_encode($reponseNormalises);
        return new Response($json);
    }


    #[Route('/new', name: 'app_reponse_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ReponseRepository $reponseRepository): Response
    {


        $entityManager = $this->getDoctrine()->getManager();
        $reponse = new Reponse();
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reponseRepository->save($reponse, true);
            $createdAt = $reponse->getcreatedAt();
            $dateCreationString = $reponse->getDateCreation()->format('Y-m-d H:i:s');
            $reponse->setcreatedAt(new \datetime);
            return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reponse/new.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'app_reponse_show', methods: ['GET'])]
    public function show(Reponse $reponse): Response
    {
        return $this->render('reponse/show.html.twig', [
            'reponse' => $reponse,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reponse_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reponse $reponse, ReponseRepository $reponseRepository): Response
    {
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reponseRepository->save($reponse, true);

            return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reponse/edit.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
        ]);
    }



    #[Route('/{id}/delete', name: 'app_reponse_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Reponse $Reponse, ReponseRepository $reponseRepository)
    { 



         $this->entityManager->remove($Reponse);
        $this->entityManager->flush();
 

        return $this->redirectToRoute('app_reponse_index');
    }

    #[Route('/new2/{id}', name: 'app_reponse_new', methods: ['GET', 'POST'])]
    public function new2($id,Request $request, ReponseRepository $reponseRepository,ManagerRegistry $doc): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $question = $doc->getRepository(Question::class)->find($id);
        $reponse = new Reponse();
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reponse->setUser($this->getUser());
            $reponse->setQuestion($question);
            $reponseRepository->save($reponse, true);

            return $this->redirectToRoute('app_reponse_new', ['id' => $question->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reponse/new2.html.twig', [
            'reponse' => $reponse, 'question'=>$question,
            'form' => $form,
        ]);
    }

     #[Route('/user/reponse/{id}/like', name: 'ReponseLike_reponse')]
    public function taskLike (Reponse $reponse,ManagerRegistry $Mg,Request $request,LikeReponseRepository $tl): Response
    {
      
        $user=$this->getUser();

        if (!$user) return $this->json(['code'=>304,'msg'=>'Unauthorized'],403);
  
        if ($reponse->isLikesByUser($user)) {
           
          $like= $tl->findOneBy(['Reponse'=>$reponse,'User'=>$user ]);
         
          $delete=$Mg->getManager();
          $delete->remove($like);
          $delete->flush();
  
          return $this->json(
              [
                  'code'=>200,
                  'msg'=>'like bien supprime',
                  'likes'=>$tl->count(['Reponse'=>$reponse]),
              ],200);
  
        }
  
        $like=new LikeReponse();
        $like->setReponse($reponse)
            ->setUser($user);
        
        
            $manger=$Mg->getManager();
        
            $manger=$Mg->getManager();
            $manger->persist($like);
            $manger->flush();
        
        
                
            return $this->json(
                [
                    'code'=>200,
                    'msg'=>'like bien ajouter',
                    'likes'=>$tl->count(['Reponse'=>$reponse]),
                ],200);


       
    }

}
