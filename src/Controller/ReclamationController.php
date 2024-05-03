<?php

namespace App\Controller;

use App\Entity\Reclamation;

use App\Entity\PdfGeneratorService;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Snappy\Pdf;
use App\Response\PdfResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Dompdf\Dompdf;
use Dompdf\Options;




/**
 * @Route("/reclamation")
 */
class ReclamationController extends AbstractController
{
  /**
 * @Route("/", name="app_reclamation_index", methods={"GET"})
 */
public function index(Request $request, ReclamationRepository $reclamationRepository): Response
{
    $sort = $request->query->get('sort', 'date'); // Par défaut, tri par date

    // Si le tri par avis est demandé
    if ($sort === 'avis') {
        $reclamations = $reclamationRepository->findBy([], ['avis' => 'DESC']);
    } else {
        $reclamations = $reclamationRepository->findAll(); // Tri par date si le paramètre de tri n'est pas avis
    }

    return $this->render('reclamation/index.html.twig', [
        'reclamations' => $reclamations,
    ]);
}



    /**
     * @Route("/new", name="app_reclamation_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ReclamationRepository $reclamationRepository): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reclamation);
            $entityManager->flush();
    
            $reclamationRepository->updateReclamationStatut(); // Mise à jour du statut des réclamations
            $reclamationRepository->updateStats(); // Mise à jour des statistiques après ajout
    
            $this->addFlash('danger', 'Réclamation envoyée avec succès');
            return $this->redirectToRoute('app_reclamation_index');
        }
    
        return $this->render('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_reclamation_show", methods={"GET"})
     */
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_reclamation_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Reclamation $reclamation): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_reclamation_index');
        }

        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_reclamation_delete", methods={"POST"})
     */
    public function delete(Request $request, Reclamation $reclamation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index');
    }
     
    



/**
 * @Route("/download-pdf-all", name="app_reclamation_downloadPdfAll", methods={"GET"})
 */
public function downloadPdfAll(ReclamationRepository $reclamationRepository): Response
{
    // Configuration de Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $options->set('isRemoteEnabled', true); // Autorise l'accès aux ressources externes
    $dompdf = new Dompdf($options);

    // Récupérer les réclamations depuis le repository
    $reclamations = $reclamationRepository->findAll();

    // Générer le contenu HTML avec le logo et les réclamations
    $htmlContent = $this->renderView('pdf/index.html.twig', [
        'reclamations' => $reclamations,
    ]);

    // Charger le contenu HTML dans Dompdf
    $dompdf->loadHtml($htmlContent);

    // Rendre le PDF
    $dompdf->render();

    // Récupérer le contenu du PDF
    $pdfContent = $dompdf->output();

    // Créer une réponse PDF à télécharger
    $response = new Response($pdfContent);

    // Définir les en-têtes pour le téléchargement du PDF
    $response->headers->set('Content-Type', 'application/pdf');
    $response->headers->set('Content-Disposition', 'attachment; filename="reclamations.pdf"');

    return $response;
}
}