<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TableRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Reservation;
use App\Form\ReservationFormType;
use App\Repository\ReservationRepository;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(ReservationRepository $ReservationRepository): Response
    {
        $reservations = $ReservationRepository->findAll();
        return $this->render('reservation/index.html.twig',
        ['reservations' => $reservations]
    );
    }

    #[Route('/tables', name: 'app_tables')]
    public function tables(TableRepository $TableRepository): Response
    {
        $tables = $TableRepository->findAll();
        return $this->render('reservation/tables.html.twig',
        ['tables' => $tables]
    );
    }

    #[Route('/reservation/{id}', name: 'app_addReservation')]
    public function addReservation(Request $request, $id): Response
    {
        $Reservation = new Reservation();
        $Reservation->setNumTable($id);

        $form = $this->createForm(ReservationFormType::class, $Reservation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($Reservation);
            $em->flush();
            return $this->redirectToRoute('app_reservation');
        }
        return $this->render('reservation/addReservation.html.twig',
        ['form' => $form->createView()]
    );
    }

    #[Route('/back', name: 'app_reservationBack')]
    public function back(ReservationRepository $ReservationRepository): Response
    {
        $reservations = $ReservationRepository->findAll();
        return $this->render('reservation/back.html.twig',
        ['reservations' => $reservations]
    );
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit($id, Request $request, ReservationRepository $ReservationRepository): Response
    {
        $reservation = $ReservationRepository->find($id);
        $form = $this->createForm(ReservationFormType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($reservation);
            $em->flush();

            return $this->redirectToRoute('app_reservationBack', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/editB', name: 'app_reservation_editB', methods: ['GET', 'POST'])]
    public function editB($id, Request $request, ReservationRepository $ReservationRepository): Response
    {
        $reservation = $ReservationRepository->find($id);
        $form = $this->createForm(ReservationFormType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($reservation);
            $em->flush();

            return $this->redirectToRoute('app_reservationBack', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/editB.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/deleteF/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete($id, Request $request, ReservationRepository $ReservationRepository): Response
    {
        $reservation = $ReservationRepository->find($id);
        $em = $this->getDoctrine()->getManager();
        
        $em->remove($reservation);
        $em->flush();
        return $this->redirectToRoute('app_reservation');
    }

    #[Route('/deleteB/{id}', name: 'app_reservation_deleteB', methods: ['POST'])]
    public function deleteB($id, Request $request, ReservationRepository $ReservationRepository): Response
    {
        $reservation = $ReservationRepository->find($id);
        $em = $this->getDoctrine()->getManager();
        
        $em->remove($reservation);
        $em->flush();
        return $this->redirectToRoute('app_reservationBack');
    }
}
