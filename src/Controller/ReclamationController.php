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
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\BarChart;
use App\Response\PdfResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;




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
 * @Route("/stats", name="app_reclamation_stats", methods={"GET"})
 */
public function stats(ReclamationRepository $reclamationRepository): Response
{
    // Récupérer le nombre de réclamations traitées
    $nbReclamationsTraitees = $reclamationRepository->countByStatut('Traitée');

    // Récupérer le nombre de réclamations en attente
    $nbReclamationsEnAttente = $reclamationRepository->countByStatut('En attente');

    // Créer le diagramme à barres
    $chart = new BarChart();
    $chart->getData()->setArrayToDataTable([
        ['Statut', 'Nombre'],
        ['Traitée', $nbReclamationsTraitees],
        ['En attente', $nbReclamationsEnAttente],
    ]);
    $chart->getOptions()->setTitle('Statistiques des Réclamations');
    $chart->getOptions()->getHAxis()->setTitle('Statut');
    $chart->getOptions()->getVAxis()->setTitle('Nombre');

    // Passer le diagramme au template pour affichage
    return $this->render('reclamation/stat.html.twig', [
        'piechart' => $chart, // Utilise piechart au lieu de chart si tu utilises un diagramme à secteurs
        'nbReclamationsTraitees' => $nbReclamationsTraitees,
        'nbReclamationsEnAttente' => $nbReclamationsEnAttente,
    ]);
}


/**
 * @Route("/download-pdf-all", name="app_reclamation_downloadPdfAll", methods={"GET"})
 */
public function downloadPdfAll(ReclamationRepository $reclamationRepository, PdfGeneratorService $pdfGeneratorService): Response
{
    // Récupérer toutes les réclamations depuis le repository
    $reclamations = $reclamationRepository->findAll();

    // Générer le contenu PDF avec toutes les réclamations
    $pdfContent = $pdfGeneratorService->generatePdf($reclamations);

    // Retourner le PDF en tant que réponse
   /* return new Response(
        $pdfContent,
        
        Response::HTTP_OK,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="reclamations.pdf"',
        ]
        
    );
    */
    return $this->render('pdf/index.html.twig', [
        'reclamations' => $reclamations,
        
    ]);

}




}