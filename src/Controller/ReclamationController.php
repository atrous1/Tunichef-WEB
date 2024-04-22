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
 * @Route("/reclamation")
 */
class ReclamationController extends AbstractController
{
    /**
     * @Route("/", name="app_reclamation_index", methods={"GET"})
     */
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
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
            $reclamationRepository->sms();

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
/*
    public function Sort(Request $request,ReclamationRepository $ReclamationRepository): Response
    {
    $Reclamations = $entityManager
    ->getRepository(Reclamation::class)
    ->findAll();

    /////////
    $back = null;
    
    if($request->isMethod("POST")){
        if ( $request->request->get('optionsRadios')){
            $SortKey = $request->request->get('optionsRadios');
            switch ($SortKey){
                case 'Avis':
                    $Reclamations = $ReclamationRepository->SortByAvis();
                    break;
                    case 'Description':
                        $Reclamations = $ReclamationRepository->SortByDescription();
                        break;


            }
        }
        else
        {
            $type = $request->request->get('optionsearch');
            $value = $request->request->get('Search');
            switch ($type){
                


                case 'Avis':
                    $Reclamations = $ReclamationRepository->findByAvis($value);
                    break;
                    case 'Description':
                        $Reclamations = $ReclamationRepository->findBydescription($value);
                        break;
             

            }
        }

        if ( $Reclamations){
            $back = "success";
        }else{
            $back = "failure";
        }
        return $this->render('Reclamation/show.html.twig', [
            'Reclamations' => $Reclamations, 'back'=> $back
        ]);
    }
}
*/
}
