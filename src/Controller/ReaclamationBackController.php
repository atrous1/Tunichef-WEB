<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/reclamationBack")
 */

class ReaclamationBackController extends AbstractController
{
     /**
     * @Route("/", name="app_reclamation_indexack", methods={"GET"})
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
    public function new(Request $request, ReclamationRepository $reclamationRepository): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamationRepository->add($reclamation);
            return $this->redirectToRoute('app_reclamation_indexack', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backReclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_reclamation_showack", methods={"GET"})
     */
    public function show(Reclamation $reclamation): Response
    {
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



