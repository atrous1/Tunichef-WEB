<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;



/**
 * @Route("/reclamationBack")
 */

class ReclamationBackController extends AbstractController
{
     /**
     * @Route("/", name="app_reclamation_indexack", methods={"GET","POST"})
     */
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('backReclamation/index.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);
    }

   
    /**
     * @Route("/new", name="app_reclamation_newack", methods={"GET", "POST"})
     */
    public function new(Request $request, ReclamationRepository $reclamationRepository, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            // Votre logique de gestion de la réclamation
             // Définition du statut en fonction de la présence d'une réponse
             $statut = $reclamation->getReponse() ? 'traité' : 'non traité';
             $reclamation->setStatut($statut);

            // Enregistrez la réclamation dans la base de données
            $entityManager->persist($reclamation);
            $entityManager->flush();

            // Redirigez ou effectuez d'autres actions nécessaires après l'enregistrement
            return $this->redirectToRoute('app_reclamation_indexack');
        }

        // Si le formulaire n'est pas valide, affichez-le à nouveau avec les erreurs
        return $this->render('backReclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_reclamation_showack", methods={"GET"})
     */
    public function show(Reclamation $reclamation = null): Response
{
    if (!$reclamation) {
        throw $this->createNotFoundException('Réclamation non trouvée');
    }

    return $this->render('backReclamation/show.html.twig', [
        'reclamation' => $reclamation,
    ]);
}

    /**
     * @Route("/{id}/edit", name="app_reclamation_editack", methods={"GET", "POST"})
     */
   public function edit(Request $request, Reclamation $reclamation): Response
{
    $form = $this->createForm(ReclamationType::class, $reclamation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $statut = $reclamation->getReponse() ? 'traité' : 'non traité';
        $reclamation->setStatut($statut);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush(); // Enregistre les modifications dans la base de données

        return $this->redirectToRoute('app_reclamation_indexack', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('backReclamation/edit.html.twig', [
        'reclamation' => $reclamation,
        'form' => $form->createView(),
    ]);
}

    /**
     * @Route("/{id}", name="app_reclamation_deleteack", methods={"POST"})
     */
    public function delete(Request $request, Reclamation $reclamation, ReclamationRepository $reclamationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $reclamationRepository->remove($reclamation);
        }

        return $this->redirectToRoute('app_reclamation_indexack', [], Response::HTTP_SEE_OTHER);
    }
    
   }

