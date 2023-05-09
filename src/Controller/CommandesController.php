<?php

namespace App\Controller;

use App\Entity\Commandes;
use App\Form\CommandesType;
use App\Repository\CommandesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Endroid\QrCode\QrCode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/commandes')]
class  CommandesController extends AbstractController
{


    #[Route('/', name: 'app_commandes_index', methods: ['GET'])]
    public function index(Request $request,CommandesRepository $commandesRepository, PaginatorInterface $paginator): Response
    {
        $commandesRepository = $this->getDoctrine()->getRepository(Commandes::class)->findAll();
        $commandesRepository = $paginator->paginate(
            $commandesRepository,
            $request->query->getInt('page',1),
            4
        );
        return $this->render('commandes/index.html.twig', [
            'commandes' => $commandesRepository
        ]);


    }


    #[Route('/new', name: 'app_commandes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CommandesRepository $commandesRepository): Response
    {
        $commande = new Commandes();
        $commande->setEtat(1);
        $form = $this->createForm(CommandesType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandesRepository->save($commande, true);
            $this->addFlash(
                'info',
                'Element ajouté avec succès'
            );

            return $this->redirectToRoute('app_commandes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commandes/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
        //$serializer = new Serializer([new ObjectNormalizer()]);
        //$formatted = $serializer->normalize($commande);
        //return new JsonResponse($formatted);

    }


    #[Route('/newUser', name: 'app_commandes_newUser', methods: ['GET', 'POST'])]
    public function newUser(Request $request, CommandesRepository $commandesRepository): Response
    {
        $commande = new Commandes();
        $commande->setEtat(0);
        $form = $this->createForm(CommandesType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandesRepository->save($commande, true);
            $this->addFlash(
                'info',
                'Element ajouté avec succès'
            );

            return $this->redirectToRoute('comm_test', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commandes/new1.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
        //$serializer = new Serializer([new ObjectNormalizer()]);
        //$formatted = $serializer->normalize($commande);
        //return new JsonResponse($formatted);

    }


    #[Route('/{id}', name: 'app_commandes_show', methods: ['GET'])]
    public function show(Commandes $commande): Response
    {
        return $this->render('commandes/show.html.twig', [
            'commande' => $commande,
        ]);
        //$commande = $this->getDoctrine()->getManager()->getRepository(Commandes::class)->findAll();
        //$serializer = new Serializer([new ObjectNormalizer()]);
        //$formatted = $serializer->normalize($commande);

        //return new JsonResponse($formatted);
    }

    #[Route('/{id}/edit', name: 'app_commandes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commandes $commande, CommandesRepository $commandesRepository): Response
    {
        $form = $this->createForm(CommandesType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandesRepository->save($commande, true);
            $this->addFlash(
                'info',
                'Element modifié avec succès'
            );


            return $this->redirectToRoute('app_commandes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commandes/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commandes_delete', methods: ['POST'])]
    public function delete(Request $request, Commandes $commande, CommandesRepository $commandesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $commandesRepository->remove($commande, true);
            $this->addFlash(
                'info',
                'Element supprimé avec succès'
            );

           // $serialize = new Serializer([new ObjectNormalizer()]);
            //$formatted = $serialize->normalize("Reclamation a ete supprimee avec success.");
            //return new JsonResponse($formatted);
        }

        return $this->redirectToRoute('app_commandes_index', [], Response::HTTP_SEE_OTHER);
        //return new JsonResponse("id commande invalide.");
    }

    #[Route('/Commande/true/{id}', name: 'accepterCommande')]
    public function acceptCoammande(\Doctrine\Persistence\ManagerRegistry $doctrine, int $id): Response
    {
        $commande = $doctrine->getRepository(Commandes::class)->find($id);
        if($commande)
        {
            $commande->setEtat(1);
            $em =$doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('app_commandes_index',['commandeFound'=>true]);
        }else
            return $this->redirectToRoute('app_commandes_index',['commandeFound'=>false]);
    }


    #[Route('/Commande/false/{id}', name: 'refuserCommande')]
    public function refuserCoammande(\Doctrine\Persistence\ManagerRegistry $doctrine, int $id): Response
    {
        $commande = $doctrine->getRepository(Commandes::class)->find($id);
        if($commande)
        {
            $commande->setEtat(-1);
            $em =$doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('app_commandes_index',['commandeFound'=>true]);
        }else
            return $this->redirectToRoute('app_commandes_index',['commandeFound'=>false]);
    }


    #[Route('/{id}/pdf.html.twig', name: 'app_commandes_pdf', methods: ['GET'])]
    public function generatePdf(Commandes $commande)
    {
        $html = $this->renderView('commandes/pdf.html.twig', [
            'commande' => $commande
        ]);

        $filename = sprintf('commande-%s.pdf.html.twig', $commande->getId());

        return new PdfResponse(
            $this->get('knp_snappy.pdf.html.twig')->getOutputFromHtml($html),
            $filename
        );
    }
    #[Route('/{id}/qrcode', name: 'app_commandes_qrcode', methods: ['GET'])]
    public function generateQrCode(Commandes $commande)
    {
        // Créer un nouvel objet QRCode
        $qrCode = new QrCode($commande->getId());

        // Modifier les options du QRCode
        $qrCode->setSize(250); // Définir la taille du QRCode en pixels

        // Générer l'image du QRCode
        $qrCodeImage = $qrCode->writeString();

        // Créer une réponse HTTP avec l'image du QRCode
        $response = new Response($qrCodeImage, Response::HTTP_OK, [
            'Content-Type' => 'image/png',
        ]);

        return $response;
    }

    /*public function getCommandes(CommandesRepository $commandesRepository, SerializerInterface $serializer)
    {
        $commande = $commandesRepository->findAll();
        //* Nous utilisons la fonction normalize qui transforme le tableau d'objets
        //* students en  tableau associatif simple.
        // $studentsNormalises = $normalizer->normalize($students, 'json', ['groups' => "students"]);

        // //* Nous utilisons la fonction json_encode pour transformer un tableau associatif en format JSON
        // $json = json_encode($studentsNormalises);

        $json = $serializer->serialize($commande, 'json', ['commande' => "commandes/show.html.twig"]);

        //* Nous renvoyons une réponse Http qui prend en paramètre un tableau en format JSON
        return new Response($json);
    }
    */
    #[Route('/json/{id}', name: 'app_commandes_show_json', methods: ['GET'])]
    public function getCommandes(CommandesRepository $commandesRepository, NormalizerInterface $normalizer): Response
    {
        $commande =$commandesRepository->findAll();
        $commandeNormalises =$normalizer->normalize($commande, 'json', ['groups'=>"commande"]);
        $json = json_encode($commandeNormalises);
        return new Response($json);
        //return $this->render('commandes/show.html.twig', [
         //   'commande' => $commande,
       // ]);
        //$commande = $this->getDoctrine()->getManager()->getRepository(Commandes::class)->findAll();
        //$serializer = new Serializer([new ObjectNormalizer()]);
        //$formatted = $serializer->normalize($commande);

        //return new JsonResponse($formatted);
    }

    #[Route("/Commandes/{id}", name: "commande")]
    public function CommandeId($id, NormalizerInterface $normalizer, CommandesRepository $repo)
    {
        $commande = $repo->find($id);
        $commandeNormalises = $normalizer->normalize($commande, 'json', ['commande' => "commande"]);
        return new Response(json_encode($commandeNormalises));
    }


    #[Route('/new/json', name: 'app_commandes_new_json', methods: ['GET', 'POST'])]
    public function addCommandeJSON(Request $request, CommandesRepository $commandesRepository,   NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $commande = new Commandes();
        $commande->setNomCommande($request->get('nom'));
        $commande->setDateCommande($request->get('date'));
        $em->persist($commande);
        $em->flush();

        $jsonContent = $Normalizer->normalize($commande, 'json', ['groups' => "commande"]);
        return new Response(json_encode($jsonContent));
    }

    #[Route("updateCommandeJSON/{id}", name: "updateCommandeJSON")]
    public function updateCommandeJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository(Commandes::class)->find($id);
        $commande->setNomCommande($req->get('nom'));
        $commande->setDateCommande($req->get('date'));

        $em->flush();

        $jsonContent = $Normalizer->normalize($commande, 'json', ['commande' => 'commande']);
        return new Response("Student updated successfully " . json_encode($jsonContent));
    }

    #[Route("deleteCommandeJSON/{id}", name: "deleteCommandeJSON")]
    public function deleteCommandeJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository( Commandes::class)->find($id);
        $em->remove($commande);
        $em->flush();
        $jsonContent = $Normalizer->normalize($commande, 'json', ['commande' => 'commande']);
        return new Response("Student deleted successfully " . json_encode($jsonContent));
    }

}
