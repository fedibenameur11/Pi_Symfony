<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Users;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Entity\AbonnementCoach;
use App\Form\AbonnementCoachType;
use App\Repository\AbonnementCoachRepository;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Programme;
use App\Entity\ProgrammeSemaine;
use App\Repository\ProgrammeRepository;
use App\Repository\ProgrammeSemaineRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\DomCrawler\Crawler;
use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\RequestStack;

class CoachingController extends AbstractController
{
    #[Route('/coachs', name: 'app_coach')]
    public function index_coachs(UserRepository $coachs): Response
    {
        return $this->render('coaching/coachs.html.twig', [
            'coachs' => $coachs->findByColumnValue('roles', 2)
        ]);
    }

    #[Route('/coaching/abonnement_confirm', name: 'app_abonnement_confirm', methods: ['GET', 'POST'])]
    public function abonnement_confirm(Request $request, AbonnementCoachRepository $abonnementCoachRepo): Response
    {
        $coach = $this->getDoctrine()
                ->getRepository(Users::class)
                ->find($request->query->get('coach'));
        $user = $this->getDoctrine()
                ->getRepository(Users::class)
                ->find(30);
        $abonnementCoach = new AbonnementCoach();
        $abonnementCoach->setCoach($coach);
        $abonnementCoach->setClient($user);
        $abonnementCoach->setStatut(false);
        $abonnementCoach->setDureeDebut(null);
        $abonnementCoach->setDureeFin(null);
        $form = $this->createForm(AbonnementCoachType::class, $abonnementCoach);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $abonnementCoachRepo->save($abonnementCoach, true);

            return $this->redirectToRoute('app_demande_abonnement_client', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('coaching/abonnement_confirm.html.twig', [
            'coach' => $coach,
            'form' => $form,
        ]);
    }

    #[Route('/coaching/abonnements_client', name: 'app_demande_abonnement_client', methods: ['GET'])]
    public function index_demande_abonnement_client(AbonnementCoachRepository $abonnementCoachRepository): Response
    {
        $result1 = $abonnementCoachRepository->findByColumnValue('client', 30);
        $result1 = $abonnementCoachRepository->sortedByColumn($result1,'statut');
        $result2 = $abonnementCoachRepository->findByColumnValue('client', 30);
        //$result2 = $abonnementCoachRepository->anotherFindByColumnValue($result2,'statut', true);
        dump($result2->getQuery()->getSQL());
        return $this->render('abonnement_coach/index_client.html.twig', [
            'demandes_abonnement' => $result1->getQuery()->getResult(),
            'abonnement_coaches' => $result2->getQuery()->getResult()
        ]);
    }

    #[Route('/coaching/abonnements_coach', name: 'app_demande_abonnement_coach', methods: ['GET'])]
    public function index_demande_abonnement_coach(AbonnementCoachRepository $abonnementCoachRepository): Response
    {
        $result1 = $abonnementCoachRepository->findByColumnValue('coach', 31);
        $result1 = $abonnementCoachRepository->sortedByColumn($result1,'statut');
        $result2 = $abonnementCoachRepository->findByColumnValue('coach', 31);
        return $this->render('abonnement_coach/index_coach.html.twig', [
            'demandes_abonnement' => $result1->getQuery()->getResult(),
            'abonnement_coaches' => $result2->getQuery()->getResult()
        ]);
    }

    #[Route('/coaching/demandes_abonnement/accept-{id}', name: 'app_demande_abonnement_accept', methods: ['POST'])]
    public function demande_accepter(Request $request, AbonnementCoach $abonnementCoach, AbonnementCoachRepository $abonnementCoachRepository): Response
    {
        if ($this->isCsrfTokenValid('update'.$abonnementCoach->getId(), $request->request->get('_token'))) {
            $abonnementCoach->setStatut(true);

            $abonnementCoach->setDureeDebut(new DateTime());

            $duree_abonnement = $abonnementCoach->getdureeAbonnement();
            $abonnementCoach->setDureeFin((new DateTime())->modify('+' . $duree_abonnement . ' months'));
            
            $abonnementCoachRepository->save($abonnementCoach,true);
            $this->generateProgram($abonnementCoach);
            
        }
            

        return $this->redirectToRoute('app_demande_abonnement_coach', [], Response::HTTP_SEE_OTHER);
    }

    public function generateProgram(AbonnementCoach $abonnementCoach): void
    {
        if ($abonnementCoach->isStatut() && $abonnementCoach->getDureeDebut() && $abonnementCoach->getDureeFin()) {
            // Create new program
            $entityManager = $this->getDoctrine()->getManager();
            $program = new Programme();
            $program->setAbonnementCoach($abonnementCoach);
            $program->setNombreSemaines($this->calculateNumberOfWeeks($abonnementCoach));
            $entityManager->persist($program);
            $entityManager->flush();

            // Create program weeks
            for ($i = 0; $i < $program->getNombreSemaines(); $i++) {
                $programWeek = new ProgrammeSemaine();
                $programWeek->setProgramme($program);
                $programWeek->setNumeroSemaine($i);
                $entityManager->persist($programWeek);

            }
            $entityManager->flush();
        }
    }

    public function calculateNumberOfWeeks(AbonnementCoach $abonnementCoach): int
    {
        $interval = $abonnementCoach->getDureeDebut()->diff($abonnementCoach->getDureeFin());
        $days = $interval->days;
        $weeks = (int) ($days / 7);
        if ($days % 7 > 0) {
            $weeks++;
        }
        if ($abonnementCoach->getDureeDebut()->format('N') >= $abonnementCoach->getDureeFin()->format('N')){
            $weeks++;
        }
        return $weeks;

        /*return (int) ceil($interval->days / 7);*/
    }

    #[Route('/coaching/demandes_abonnement/delete-{id}', name: 'app_demande_abonnement_delete', methods: ['POST'])]
    public function delete(Request $request, AbonnementCoach $abonnementCoach, AbonnementCoachRepository $abonnementCoachRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$abonnementCoach->getId(), $request->request->get('_token'))) {
            $abonnementCoachRepository->remove($abonnementCoach, true);
        }

        return $this->redirectToRoute('app_demande_abonnement_coach', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/coaching/abonnements_coach/show_info-{id}', name: 'app_abonnement_coach_show_infos', methods: ['GET'])]
    public function show(AbonnementCoach $abonnementCoach): Response
    {
        return $this->render('abonnement_coach/show.html.twig', [
            'abonnement_coach' => $abonnementCoach,
        ]);
    }

    #[Route('/coaching/abonnements_coach/programme-{id}', name: 'app_abonnement_coach_programme_infos', methods: ['GET'])]
    public function showProgramme(Programme $programme): Response
    {
        return $this->render('programme/show.html.twig', [
            'programme' => $programme,
        ]);
        
    }
    
    #[Route('/coaching/abonnements_coach/programme-{id1}/renderpdf-{id2}',
    name: 'app_abonnement_coach_programme_renderpdf', methods: ['GET'])]
    #[ParamConverter('programme', options: ['mapping' => ['id1' => 'id']])]
    #[ParamConverter('programmesemaine', options: ['mapping' => ['id2' => 'id']])]
    public function render_pdf(Programme $programme, ProgrammeSemaine $programmesemaine): Response
    {
            // Get the HTML content of the twig template
        $html = $this->renderView('programme/show1.html.twig', [
            'programme' => $programme,
            'programmesemaine' => $programmesemaine
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', true); // Allow Dompdf to access remote assets
        
        // Instantiate Dompdf
        $dompdf = new Dompdf($options);
        // Load the HTML content into Dompdf
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A3', 'landscape');
        // Render the PDF
        $dompdf->render();
        // Set up the response
        $pdf = $dompdf->output();
        // Send the PDF as a response with appropriate headers
        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="programme.pdf"',
        ]);
    }

}
