<?php

namespace App\Controller;

use App\Entity\AbonnementCoach;
use App\Form\AbonnementCoachType;
use App\Repository\AbonnementCoachRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

use App\Controller\CoachingController;

#[Route('/abonnements_coach')]
class AbonnementCoachController extends AbstractController
{
    #[Route('/', name: 'app_abonnement_coach_index', methods: ['GET'])]
    public function index(AbonnementCoachRepository $abonnementCoachRepository): Response
    {
        return $this->render('abonnement_coach/index.html.twig', [
            'abonnement_coaches' => $abonnementCoachRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_abonnement_coach_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AbonnementCoachRepository $abonnementCoachRepository): Response
    {
        $abonnementCoach = new AbonnementCoach();
        $form = $this->createForm(AbonnementCoachType::class, $abonnementCoach);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $abonnementCoachRepository->save($abonnementCoach, true);
            if ($abonnementCoach->isStatut()){
                $generateProgram = new CoachingController();
                $generateProgram->generateProgram($abonnementCoach);
            }
            return $this->redirectToRoute('app_abonnement_coach_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('abonnement_coach/new.html.twig', [
            'abonnement_coach' => $abonnementCoach,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_abonnement_coach_show', methods: ['GET'])]
    public function show(AbonnementCoach $abonnementCoach): Response
    {
        return $this->render('abonnement_coach/show.html.twig', [
            'abonnement_coach' => $abonnementCoach,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_abonnement_coach_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AbonnementCoach $abonnementCoach, AbonnementCoachRepository $abonnementCoachRepository): Response
    {
        $form = $this->createForm(AbonnementCoachType::class, $abonnementCoach);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $abonnementCoachRepository->save($abonnementCoach, true);

            return $this->redirectToRoute('app_abonnement_coach_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('abonnement_coach/edit.html.twig', [
            'abonnement_coach' => $abonnementCoach,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_abonnement_coach_delete', methods: ['POST'])]
    public function delete(Request $request, AbonnementCoach $abonnementCoach, AbonnementCoachRepository $abonnementCoachRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$abonnementCoach->getId(), $request->request->get('_token'))) {
            $abonnementCoachRepository->remove($abonnementCoach, true);
        }

        return $this->redirectToRoute('app_abonnement_coach_index', [], Response::HTTP_SEE_OTHER);
    }

    /*******************************JSON************************** */
    #[Route('/json/prod', name: 'app_produit_index_json', methods: ['GET'])]
    public function index_json(NormalizerInterface $normalizer,AbonnementCoachRepository $prod_res): Response
    {
        $abonnementCoach= $prod_res->findAll();

        $produitsNormalises = $normalizer->normalize($abonnementCoach, 'json', ['groups' => "abonnement_coach"]);
        $json = json_encode($produitsNormalises);

        return new Response($json);
    }
    #[Route('/json/prod/{id}', name: 'app_produit_index_json_id')]
    public function index_json_id($id,NormalizerInterface $normalizer,AbonnementCoachRepository $prod_res): Response
    {
        $prod= $prod_res->find($id);

        $produitsNormalises = $normalizer->normalize($prod, 'json', ['groups' => "abonnement_coach"]);
        $json = json_encode($produitsNormalises);

        return new Response($json);
    }
    #[Route('/json/prod/new/a', name: 'app_produit_index_json_new')]
    public function index_json_new(Request $req,NormalizerInterface $normalizer): Response
    {
        $ok2 = 2;
        $ok = false;
        if ($req->get('statut')){
            $ok= true;
        }
        else{
            $ok= false;
        }
        $em = $this ->getDoctrine()->getManager();
        $prod = new AbonnementCoach();
        $prod->setDureeAbonnement($req->get('duree_abonnement'));
        $prod->setStatut(true);
        $generateProgram = new CoachingController();
        $generateProgram->generateProgram($prod);
        $em->persist($prod);
        $em->flush();
        /*$req->get('statut')*/

        $produitsNormalises = $normalizer->normalize($prod, 'json', ['groups' => "abonnement_coach"]);
        $json = json_encode($produitsNormalises);

        return new Response($json);
    }
    #[Route("/json/prod/modif/a/{id}", name: "app_produit_index_json_modif")]
    public function index_json_modif(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $prod = $em->getRepository(AbonnementCoach::class)->find($id);
        $prod->setDureeAbonnement($req->get('duree_abonnement'));
        $prod->setStatut(false);

        $em->flush();

        $jsonContent = $Normalizer->normalize($prod, 'json', ['groups' => 'abonnement_coach']);
        return new Response("Produit updated successfully " . json_encode($jsonContent));
    }
    #[Route("/json/prod/supr/{id}", name: "app_produit_index_json_suppr")]
    public function deleteStudentJSON($id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $prod = $em->getRepository(AbonnementCoach::class)->find($id);
        $em->remove($prod);
        $em->flush();
        $jsonContent = $Normalizer->normalize($prod, 'json', ['groups' => 'abonnement_coach']);
        return new Response("Produit deleted successfully " . json_encode($jsonContent));
    }


}
