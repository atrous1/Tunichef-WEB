<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Evenement;
use App\Entity\Promotion;
use App\Form\EvenementType;
use App\Form\PromotionType;
use App\Repository\EvenementRepository;
use App\Repository\PromotionRepository;


use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

use DateTime;

use Facebook\Facebook;

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Serializer\Encoder\JsonEncoder;



class EvenementController extends AbstractController
{
    #[Route('/dash/admin/events', name: 'dash_events')]
    public function eventsList(EvenementRepository $evenementRepository): Response
    {
        $events = $evenementRepository->findAll();

        return $this->render('evenement/dash-admin-events.html.twig', [
            'controller_name' => 'EvenementController',
            'events' => $events,
        ]);
    }

    #[Route('/events', name: 'user_events')]
    public function userEventsList(EvenementRepository $evenementRepository): Response
    {
        $events = $evenementRepository->findAll();

        return $this->render('evenement/user-events-list.html.twig', [
            'controller_name' => 'EvenementController',
            'events' => $events,
        ]);
    }

    #[Route('/event-detail/{id}', name: 'user_eventDetail')]
    public function userEventDetail(ManagerRegistry $doctrine, $id): Response
    {
        $event = $doctrine->getRepository(Evenement::class)->find($id);

        return $this->render('evenement/user-event-details.html.twig', [
            'controller_name' => 'EvenementController',
            'event' => $event,
        ]);
    }


    #[Route('/dash/admin/event/add', name: 'add_event')]
    public function addEvent(Request $request, ManagerRegistry $doctrine,  SluggerInterface $slugger): Response
    {
        $event = new Evenement();
        $form = $this->createForm(EvenementType::class, $event);
        $form->handleRequest($request);
   
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();

            //******************************* */
            $image = $form->get('image')->getData();
    
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        $this->getParameter('files_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $event->setImage($newFilename) ;
            }
            //***************************** */
            // Créer un nouvel objet DateTime pour la date actuelle
            $date = new DateTime();
            $dateOnly = new \DateTime($date->format('Y-m-d'));
            $event->setEventDate($dateOnly);

            $em->persist($event);//ajoute a base
            $em->flush();//update
            return $this->redirectToRoute("dash_events");
        }

        return $this->renderForm('evenement/dash-admin-event-add.html.twig', [
            'title' => 'Pi project -- DashAdminEvents',
            "formEvent" => $form,
        ]);
    }

    #[Route('/dash/admin/events/deleteEvent/{id}', name: 'deleteEvent')]
    public function deleteEvent(ManagerRegistry $repo, $id): Response
    {

        $event = $repo->getRepository(Evenement::class)->find($id);
        $em = $repo->getManager();
        $em->remove($event);
        $em->flush();

        return $this->redirectToRoute('dash_events');
    }


    #[Route('/dash/admin/event/update/{id}', name: 'update_event')]
    public function updateEvent(Request $request, $id, ManagerRegistry $doctrine,  SluggerInterface $slugger): Response
    {
        $event = $doctrine->getRepository(Evenement::class)->find($id);

        $form = $this->createForm(EvenementType::class, $event);
        $form->handleRequest($request);
   
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();

            //******************************* */
            $image = $form->get('image')->getData();
    
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        $this->getParameter('files_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $event->setImage($newFilename) ;
            }
            //***************************** */
            // Créer un nouvel objet DateTime pour la date actuelle
           // $date = new DateTime();
           // $dateOnly = new \DateTime($date->format('Y-m-d'));
           // $event->setEventDate($dateOnly);

            $em->flush();
            return $this->redirectToRoute("dash_events");
        }

        return $this->renderForm('evenement/dash-admin-event-update.html.twig', [
            'title' => 'Pi project -- DashAdminEvents',
            "formEvent" => $form,
            "event" =>$doctrine->getRepository(Evenement::class)->find($id),
        ]);
    }

    //******************************************promotions ************************/
    #[Route('/dash/admin/eventPromo/{id}', name: 'dash_eventPromo')]
    public function eventPromoList(ManagerRegistry $doctrine, $id): Response
    {
        $promotions = $doctrine->getRepository(Promotion::class)->findBy(['event' => $id]);

        return $this->render('evenement/dash-admin-event-promo-list.html.twig', [
            'controller_name' => 'EvenementController',
            'promotions' => $promotions,
        ]);
    }


    #[Route('/dash/admin/promo/add/{id}', name: 'add_promo')]
    public function addPromo(Request $request, $id, ManagerRegistry $doctrine,  SluggerInterface $slugger): Response
    {
        $event = $doctrine->getRepository(Evenement::class)->find($id);

        $promo = new Promotion();
        $form = $this->createForm(PromotionType::class, $promo);
        $form->handleRequest($request);
   
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();

            $promo->setEvent($event);
            $em->persist($promo);
            $em->flush();
            return $this->redirectToRoute("dash_eventPromo", ['id' => $id]);
        }

        return $this->renderForm('evenement/dash-admin-event-promo-add.html.twig', [
            'title' => 'Pi project -- DashAdminEvents',
            "formPromo" => $form,
        ]);
    }

    #[Route('/dash/admin/promo/deletePromo/{id}/{idPromo}', name: 'deletePromo')]
    public function deletePromo(ManagerRegistry $repo, $id,$idPromo): Response
    {

        $promo = $repo->getRepository(Promotion::class)->find($idPromo);
        $em = $repo->getManager();
        $em->remove($promo);
        $em->flush();

        return $this->redirectToRoute("dash_eventPromo", ['id' => $id]);
    }

    #[Route('/dash/admin/promo/update/{id}/{idPromo}', name: 'update_promo')]
    public function updatePromo(Request $request, $id,$idPromo ,ManagerRegistry $doctrine,  SluggerInterface $slugger): Response
    {

        $promo = $doctrine->getRepository(Promotion::class)->find($idPromo);

        $form = $this->createForm(PromotionType::class, $promo);
        $form->handleRequest($request);
   
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();

            $em->flush();
            return $this->redirectToRoute("dash_eventPromo", ['id' => $id]);
        }

        return $this->renderForm('evenement/dash-admin-event-promo-update.html.twig', [
            'title' => 'Pi project -- DashAdminEvents',
            "formPromo" => $form,
        ]);
    }

    //****************************************************************** */
    #[Route('/facebook_share/{id}', name: 'facebook_share')]
    public function facebook_share(ManagerRegistry $doctrine, Request $request,$id): Response
    {
        $event = $doctrine->getRepository(Evenement::class)->find($id);
        

        $fb = new Facebook([
            'app_id' => '2261837737339189',
            'app_secret' => 'f14df88c3889e59a98a697d328b00fe2',
            'default_graph_version' => 'v16.0',
            'default_access_token' => 'EAAgJISLfqTUBO7U51KcdTxtWgZCwPcPjz3EaSAfZCJH2D3b4Kr96iD9ornivl0262kWxZC6NySh7pmHTxbuWBdsZBlFTZASy66XMT5CxLsTzmOe8Tt8Xk20z0oubGULYIeq412db8o4ByQbPbEB9qW7xUKcGyvmExRO9kQmWsC7uyeCEZCZADdu3laZBveVMYbi2lTbCWRYETPNBNnzs1IDc4gcZD',
        ]);
        
     
        $message ='New Event : '. '                                                                ' 
                    .'Name: '.$event->getEventName() . '                                                                                 '  
                    .'**Description: '. $event->getDescription() . '                                                                                        ' 
                    .'**Discount on many products !! ' . '                                                                                                                                         ' 
                    .'**Date: '. $event->getEventDate()->format('d-m-Y');
        $data = [
            'message' => $message 
        ];
        
        try {
            // Tenter de faire le post sur Facebook
            $response = $fb->post('/me/feed', $data);
    
            // Vérifier si une erreur est survenue
            if ($response->isError()) {
                // Gérer l'erreur si nécessaire
            } else {
                // Post partagé avec succès
            }
        } catch (\Exception $e) {
            // 
        }
        // return new Response('updated!');
        return $this->redirectToRoute('dash_events');
    }


    #[Route('products/searchEvent', name: 'searchEvent')]
    public function searchEvent(EvenementRepository $evenementRepository, Request $request): Response
    {
        $value =$request->get('searchEventData');
        $events = $evenementRepository->searchEventFunction($value);    
        // Convertir les objets en tableau associatif
        $eventsArray = [];
        foreach ($events as $event) {
            $eventArray = [
                'eventID' => $event->getEventID(),
                'eventName' => $event->getEventName(),
                'eventDate' => $event->getEventDate(),
                'description' => $event->getDescription(),
                'image' => $event->getImage(),
            ];
            $eventsArray[] = $eventArray;
        }

        // Convertir le tableau associatif en chaîne JSON
        $jsonResponse = json_encode($eventsArray);

        // Afficher la réponse JSON
        return new Response ($jsonResponse);
    }
}
