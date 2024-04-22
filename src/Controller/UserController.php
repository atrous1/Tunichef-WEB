<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\UserFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/front', name: 'app_user_front')]
    public function front(UserRepository $UserRepository): Response
    {
        $users = $UserRepository->findAll();
        return $this->render('user/home.html.twig', ['users' => $users]);
    }

    #[Route('/user/back', name: 'app_user_back')]
    public function back(UserRepository $UserRepository): Response
    {
        $users = $UserRepository->findAll();
        return $this->render('user/admin.html.twig', ['users' => $users]);
    }

    #[Route('/user/add', name: 'app_user_add')]
    public function Add(Request $request,UserPasswordEncoderInterface $passwordEncoder)
    {
        $User = new User();
        $form = $this->createForm(UserFormType::class, $User);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $image = $form->get('image')->getData();
            if($image) // ajout image
            {
                $fileName = md5(uniqid()).'.'.$image->guessExtension();
                $image->move($this->getParameter('files_directory'), $fileName);
                $User->setImage($fileName);
            }
            
            $User->setPassword($passwordEncoder->encodePassword($User, $User->getPassword()));
            $em->persist($User);
            $em->flush();
            return $this->redirectToRoute('app_login');
        }
        return $this->render('user/signup.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/user/edit/{id}', name: 'app_user_edit')]
    public function edit($id, UserRepository $repository, ManagerRegistry $doctrine, Request $request, UserPasswordEncoderInterface $passwordEncoder) : Response
    {
        
        $user=$repository->find($id);
        $originalFile = $user->getImage();
        $form=$this->createForm(UserFormType::class,$user);
        $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid()){ 
            $em=$doctrine->getManager();
            $file = $form->get('image')->getData(); // reçoit le file uploaded
        
            if ($file) {
                // génére un filename unique
                $newFilename = md5(uniqid()) . '.' . $file->guessExtension();
        
                // déplace le file dans files_directory
                $file->move(
                    $this->getParameter('files_directory'),
                    $newFilename
                );
        
                // modifie l'entité avec le nouveau file
                $user->setImage($newFilename);
        
                // supprimer l'original file s'il existe
                if ($originalFile) {
                    $originalFilePath = $this->getParameter('files_directory') . '/' . $originalFile;
                    if (file_exists($originalFilePath)) {
                        unlink($originalFilePath);
                    }
                }
            } else {
                // utilise l'original file
                $user->setImage($originalFile);
            }
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));
            $em->flush();
            return $this->redirectToRoute("app_user_front");
        }
        return $this->renderForm('user/edit.html.twig', 
        [
            'user' => $user,
            'form' => $form,
        ]);
    
    }

    #[Route('/user/editB/{id}', name: 'app_user_editB')]
    public function editBack($id, UserRepository $repository, ManagerRegistry $doctrine, Request $request, UserPasswordEncoderInterface $passwordEncoder) : Response
    {
        
        $user=$repository->find($id);
        $originalFile = $user->getImage();
        $form=$this->createForm(UserFormType::class,$user);
        $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid()){ 
            $em=$doctrine->getManager();
            $file = $form->get('image')->getData(); // reçoit le file uploaded
        
            if ($file) {
                // génére un filename unique
                $newFilename = md5(uniqid()) . '.' . $file->guessExtension();
        
                // déplace le file dans files_directory
                $file->move(
                    $this->getParameter('files_directory'),
                    $newFilename
                );
        
                // modifie l'entité avec le nouveau file
                $user->setImage($newFilename);
        
                // supprimer l'original file s'il existe
                if ($originalFile) {
                    $originalFilePath = $this->getParameter('files_directory') . '/' . $originalFile;
                    if (file_exists($originalFilePath)) {
                        unlink($originalFilePath);
                    }
                }
            } else {
                // utilise l'original file
                $user->setImage($originalFile);
            }
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));
            $em->flush();
            return $this->redirectToRoute("app_user_back");
        }
        return $this->renderForm('user/editBack.html.twig', 
        [
            'user' => $user,
            'form' => $form,
        ]);
    
    }


    #[Route('/user/delete/{id}', name: 'app_user_deleteF')]
    public function deleteFront($id, Request $request, UserRepository $UserRepository)
    {

        $user = $UserRepository->find($id);
        $em = $this->getDoctrine()->getManager();
        
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('app_user_front');
    }

    #[Route('/user/deleteB/{id}', name: 'app_user_deleteB')]
    public function deleteBack($id, Request $request, UserRepository $UserRepository)
    {

        $user = $UserRepository->find($id);
        $em = $this->getDoctrine()->getManager();
        
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('app_user_back');
    }

    #[Route('/home', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('user/home.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
