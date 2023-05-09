<?php

namespace App\Controller;

use App\Entity\AbonnementSalle;
use App\Entity\Salle;
use App\Form\AbonnementSalleType;
use App\Form\AbonnementSalleEditType;
use App\Repository\AbonnementSalleRepository;
use App\Repository\SalleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use DateTime;
use Twilio\Rest\Client;

#[Route('/abonnement/salle')]
class AbonnementSalleController extends AbstractController
{
    #[Route('/', name: 'app_abonnement_salle_index', methods: ['GET'])]
    public function index(AbonnementSalleRepository $abonnementSalleRepository): Response
    {
        return $this->render('abonnement_salle/index.html.twig', [
            'abonnement_salles' => $abonnementSalleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_abonnement_salle_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AbonnementSalleRepository $abonnementSalleRepository): Response
    {
        $abonnementSalle = new AbonnementSalle();
        $form = $this->createForm(AbonnementSalleType::class, $abonnementSalle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $abonnementSalleRepository->save($abonnementSalle, true);

            return $this->redirectToRoute('app_abonnement_salle_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('abonnement_salle/new.html.twig', [
            'abonnement_salle' => $abonnementSalle,
            'form' => $form,
        ]);
    }
    #[Route('/neww', name: 'app_abonnement_salle_neww', methods: ['GET', 'POST'])]
    public function neww(Request $request, AbonnementSalleRepository $abonnementSalleRepository): Response
    {
        $abonnementSalle = new AbonnementSalle();
        $form = $this->createForm(AbonnementSalleType::class, $abonnementSalle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $abonnementSalleRepository->save($abonnementSalle, true);

            /* $now = new DateTime();

             // Obtient la date et l'heure sous forme de chaîne de caractères
             $dateAndTime = $now->format('Y-m-d H:i:s');

             // Récupérer le numéro de téléphone du gamer à partir de la requête
             $gamerPhoneNumber = '+21652467059';

             // Remplacer les valeurs par les identifiants d'authentification Twilio ou Nexmo
             $sid = 'AC3dcac380e1fbb675f3d2e176455f1729';
             $token = '0683b355761ace035214de4fef4d7653';
             $fromNumber = '+13157911937';
             $toNumber = $gamerPhoneNumber;

             // Instancier un objet client Twilio ou Nexmo
             $client = new \Twilio\Rest\Client($sid, $token); // ou new \Nexmo\Client(new \Nexmo\Client\Credentials\Basic($sid, $token));

             // Envoyer un SMS avec la date d'aujourd'hui dans le message
             $client->messages->create(
                  $toNumber,
                  [
                    'from' => $fromNumber,
                    'body' => 'Votre abonnement a été effectué avec succés le' . $dateAndTime
                  ]
            );
         */
        }
        $this->addFlash('success', 'Article Created! Knowledge is power!');

        return $this->renderForm('abonnement_salle/neww.html.twig', [
            'abonnement_salle' => $abonnementSalle,
            'form' => $form,

        ]);
        /*$this->addFlash('success', 'Article Created! Knowledge is power!');*/
    }

    /*#[Route('/json1', name: 'app_abonnement_salle_neww_json1', methods: ['GET', 'POST'])]
    public function addAonnement(Request $request, AbonnementSalleRepository $abonnementSalleRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $abonnementSalle = new AbonnementSalle();
        $abonnementSalle = 
        $form = $this->createForm(AbonnementSalleType::class, $abonnementSalle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $abonnementSalleRepository->save($abonnementSalle, true);
        }

        return $this->renderForm('abonnement_salle/neww.html.twig', [
            'abonnement_salle' => $abonnementSalle,
            'form' => $form,

        ]);
        
    }*/

    #[Route('/json1/{duree}/{id}', name: 'app_abonnement_salle_neww_json1', methods: ['GET', 'POST'])]
    public function addAbonnement(SalleRepository $sallerepo ,Request $request,$duree, $id, AbonnementSalleRepository $abonnementSalleRepository, NormalizerInterface $Normalizer): Response
    {
        //$form = $this->createForm(AddAbonnementSalleType::class);
        //$form->handleRequest($request);

        //if ($form->isSubmitted() && $form->isValid()) {
            $salle = $sallerepo->find($id);
            $em = $this->getDoctrine()->getManager();
            $abonnementSalle = new AbonnementSalle();
            $abonnementSalle->setDureeAbonnement($duree);
            $abonnementSalle->setSalle($salle);
           // $duree_abonnement = $form->getData()['duree_abonnement'];
            $em->persist($abonnementSalle);
            $em->flush();
            //$abonnementSalleRepository->save($abonnementSalle, true);
            $jsonContent = $Normalizer->normalize( $abonnementSalle, 'json', ['groups' => "abonnements"]);
            return new Response(json_encode($jsonContent));
            //return $this->redirectToRoute('app_salle_index2');
        }


    #[Route('/{id}', name: 'app_abonnement_salle_show', methods: ['GET'])]
    public function show(AbonnementSalle $abonnementSalle): Response
    {
        return $this->render('abonnement_salle/show.html.twig', [
            'abonnement_salle' => $abonnementSalle,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_abonnement_salle_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AbonnementSalle $abonnementSalle, AbonnementSalleRepository $abonnementSalleRepository): Response
    {
        $form = $this->createForm(AbonnementSalleEditType::class, $abonnementSalle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $abonnementSalleRepository->save($abonnementSalle, true);

            return $this->redirectToRoute('app_abonnement_salle_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('abonnement_salle/edit.html.twig', [
            'abonnement_salle' => $abonnementSalle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_abonnement_salle_delete', methods: ['GET', 'POST'])]
    /*public function delete(Request $request, AbonnementSalle $abonnementSalle, AbonnementSalleRepository $abonnementSalleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$abonnementSalle->getId(), $request->request->get('_token'))) {
            $abonnementSalleRepository->remove($abonnementSalle, true);
        }

        return $this->redirectToRoute('app_abonnement_salle_index', [], Response::HTTP_SEE_OTHER);
    }*/

    public function delete(Request $request, AbonnementSalle $salle)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($salle);
        $entityManager->flush();

        return $this->redirectToRoute('app_abonnement_salle_index');
    }
}
