<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Categorie;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;

#[Route('/produit')]
class ProduitController extends AbstractController
{
    #[Route('/', name: 'app_produit_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Produit::class);
        

    $queryBuilder = $repository->createQueryBuilder('p');

    // Récupérer les critères de filtrage depuis la requête
    $name = $request->query->get('name');
    $category = $request->query->get('category');
    $priceMin = $request->query->get('priceMin');
    $priceMax = $request->query->get('priceMax');

    // Ajouter les critères de filtrage à la requête
    if ($name) {
        $queryBuilder->andWhere('p.nom LIKE :name')
            ->setParameter('name', '%'.$name.'%');
    }
    if ($category) {
        $queryBuilder
        ->andwhere('p.cat = :category')
    ->setParameter('category', $category);
    }
    if ($priceMin) {
        $queryBuilder->andWhere('p.prix >= :priceMin')
            ->setParameter('priceMin', $priceMin);
    }
    if ($priceMax) {
        $queryBuilder->andWhere('p.prix <= :priceMax')
            ->setParameter('priceMax', $priceMax);
    }

    // Trier les résultats par ordre alphabétique du nom de produit
    //$queryBuilder->orderBy('p.nom', 'ASC');
    

    $produits = $queryBuilder->getQuery()->getResult();

    // Passer les produits filtrés et triés à la vue pour l'affichage
    return $this->render('produit/index.html.twig', [
        'produits' => $produits,
        'name' => $name,
        'category' => $category,
        'priceMin' => $priceMin,
        'priceMax' => $priceMax,
        
    ]);





    // Passer les produits triés à la vue pour l'affichage
    return $this->render('produit/index.html.twig', ['produits' => $produits]);
    }

    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProduitRepository $produitRepository,SluggerInterface $slugger): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('imageP')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('produit_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $produit->setImageP($newFilename);
            }
            $produitRepository->save($produit, true);

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, ProduitRepository $produitRepository,SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('imageP')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('produit_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $produit->setImageP($newFilename);
            }
            $produitRepository->save($produit, true);

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_produit_delete', methods: ['GET','POST'])]
    /*public function delete(Request $request, Produit $produit, ProduitRepository $produitRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $produitRepository->remove($produit, true);
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }*/
    public function delete(Request $request, Produit $produit)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($produit);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_produit_index');
    }
    #[Route('/front/prod/{id}', name: 'categorie_show_p', methods: ['GET'])]
    /*public function produitsParCategorie($categorieId) 
{
    // Récupération de l'entité Catégorie
    $categorie = $this->getDoctrine()->getRepository(Categorie::class)->find($categorieId);

    // Récupération de tous les produits associés à la catégorie
    $produits = $this->getDoctrine()->getRepository(Produit::class)->findBy(['categorie' => $categorie]);

    // Ou retourner une vue avec les produits
    return $this->render('produit/prodH.html.twig', [
        'produits' => $produits
    ]);
}*/

public function produitsParCategorie($id,Request $request, EntityManagerInterface $entityManager)
    {
        // Récupération de l'entité Catégorie
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->find($id);

        // Vérification si la catégorie existe
        if (!$categorie) {
            throw $this->createNotFoundException('La catégorie n\'existe pas');
        }

        // Récupération de tous les produits associés à la catégorie
        $produits = $categorie->getProds();
        $queryBuilder = $entityManager->createQueryBuilder()
        ->select('p')
    ->from('App\Entity\Produit', 'p')
    ->where('p.cat = :categorie')
    ->setParameter('categorie', $categorie);

        // Récupérer les critères de filtrage depuis la requête
        $name = $request->query->get('name');
        $priceMin = $request->query->get('priceMin');
        $priceMax = $request->query->get('priceMax');
        $sortField = $request->query->get('sortField', 'nom');
        $sortOrder = $request->query->get('sortOrder', 'ASC');
        if ($name) {
            $queryBuilder->andWhere('p.nom LIKE :name')
                ->setParameter('name', '%'.$name.'%');
        }

        if ($priceMin) {
            $queryBuilder->andWhere('p.prix >= :priceMin')
                ->setParameter('priceMin', $priceMin);
        }
        if ($priceMax) {
            $queryBuilder->andWhere('p.prix <= :priceMax')
                ->setParameter('priceMax', $priceMax);
        }
        //$queryBuilder->orderBy('p.nom', 'ASC');
        if ($sortField == 'nom') {
            $queryBuilder->orderBy('p.nom', $sortOrder);
        } elseif ($sortField == 'prix') {
            $queryBuilder->orderBy('p.prix', $sortOrder);
        }
        $produits = $queryBuilder->getQuery()->getResult();
        return $this->render('produit/prodH.html.twig', [
            'produits' => $produits,
            'name' => $name,
            'priceMin' => $priceMin,
            'priceMax' => $priceMax,
            'sortField' => $sortField,
        'sortOrder' => $sortOrder,
        ]);

        // Ou retourner une vue avec les produits
        return $this->render('produit/prodH.html.twig', [
            'produits' => $produits,
        ]);
    }
    /*******************************JSON************************** */
    #[Route('/json/prod', name: 'app_produit_index_json', methods: ['GET'])]
    public function index_json(NormalizerInterface $normalizer,ProduitRepository $prod_res): Response
    {
        $prod= $prod_res->findAll();

        $produitsNormalises = $normalizer->normalize($prod, 'json', ['groups' => "prod"]);
        $json = json_encode($produitsNormalises);

        return new Response($json);
    }
    #[Route('/json/prod/{id}', name: 'app_produit_index_json_id')]
    public function index_json_id($id,NormalizerInterface $normalizer,ProduitRepository $prod_res): Response
    {
        $prod= $prod_res->find($id);

        $produitsNormalises = $normalizer->normalize($prod, 'json', ['groups' => "prod"]);
        $json = json_encode($produitsNormalises);

        return new Response($json);
    }
    #[Route('/json/prod/new/a', name: 'app_produit_index_json_new')]
    public function index_json_new(Request $req,NormalizerInterface $normalizer): Response
    {
        $em = $this ->getDoctrine()->getManager();
        $prod = new Produit();
        $prod->setNom($req->get('nom'));
        $prod->setPrix($req->get('prix'));
        $prod->setQuantite($req->get('quantite'));
        $prod->setPoids($req->get('poids'));
        $prod->setImageP($req->get('imageP'));
        $em->persist($prod);
        $em->flush();
        

        $produitsNormalises = $normalizer->normalize($prod, 'json', ['groups' => "prod"]);
        $json = json_encode($produitsNormalises);

        return new Response($json);
    }
    #[Route("json/prod/{id}/modif/a", name: "app_produit_index_json_modif")]
    public function index_json_modif(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $prod = $em->getRepository(Produit::class)->find($id);
        $prod->setNom($req->get('nom'));
        $prod->setPrix($req->get('prix'));
        $prod->setQuantite($req->get('quantite'));
        $prod->setPoids($req->get('poids'));
        $prod->setImageP($req->get('imageP'));

        $em->flush();

        $jsonContent = $Normalizer->normalize($prod, 'json', ['groups' => 'prod']);
        return new Response("Produit updated successfully " . json_encode($jsonContent));
    }
    #[Route("/json/prod/supr/{id}", name: "app_produit_index_json_suppr")]
    public function deleteStudentJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $prod = $em->getRepository(Produit::class)->find($id);
        $em->remove($prod);
        $em->flush();
        $jsonContent = $Normalizer->normalize($prod, 'json', ['groups' => 'prod']);
        return new Response("Produit deleted successfully " . json_encode($jsonContent));
    }
    /***********SEND EMAIL */
    #[Route("/aqwzs/sb", name: "app_produit_index_json_supprvssv")]
    public function sendEmail($name)
    {
        
        $email = (new Email())
        ->from('test.symf123@gmail.com')
        ->to('test.symf123@gmail.com')
        ->subject('produit quantite faible')
        ->text("la quantite de ce produit $name est trés faible");
        $transport=new GmailSmtpTransport('test.symf123@gmail.com','qwdforyxtsgcmwvz');
        $mailer=new Mailer($transport);
        $mailer->send($email);
        return new Response("email envoyée avec succéess " );

    }
    #[Route("/aqwzsx/sbv", name: "app_produit_index_json_supprvssvi")]
    public function check_quantite(ProduitRepository $prod_res, EntityManagerInterface $entityManager)
    {   $repository = $entityManager->getRepository(Produit::class);

        $queryBuilder = $repository->createQueryBuilder('p');
        $queryBuilder->orderBy('p.nom', 'ASC');
        $prod = $queryBuilder->getQuery()->getResult();
        
        return $this->render('produit/testEmail.html.twig', [
            'produits' => $prod,
        ]);

    }
    
}

