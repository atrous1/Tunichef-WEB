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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Form\EditUserBackFormType;


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
            $User->setRole("CLIENT");
            $User->setStatut("ACTIF");
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
        return $this->renderForm('user/editFront.html.twig', 
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
        $form=$this->createForm(EditUserBackFormType::class,$user);
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
    public function deleteFront($id, UserRepository $UserRepository, Request $request, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, AuthenticationUtils $authenticationUtils)
    {
        $utilisateur = $UserRepository->find($id);
        if ($this->isCsrfTokenValid('delete' . $utilisateur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($utilisateur);
            $entityManager->flush();

            // Déconnexion manuelle de l'utilisateur
            $tokenStorage->setToken(null);
            $request->getSession()->invalidate();

            // Rediriger vers une page de confirmation après la suppression
            $this->addFlash('success', 'Votre compte a été supprimé avec succès.');
            return $this->render('user/account_deleted.html.twig');
        }
        //return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        return $this->render('user/signup.html.twig');
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

    #[Route('/home', name: 'app_home', methods: ["GET", "POST"])]
    public function home(Request $request): Response
{
    $user = $this->getUser();
    $warningMessage = $request->query->get('warningMessage');

    return $this->render('user/home.html.twig', [
        'user' => $user,
        'warningMessage' => $warningMessage,
    ]);
}


    #[Route('/profil/{id}', name: 'app_profil')]
    public function profil($id, UserRepository $UserRepository): Response
    {
        $user = $UserRepository->find($id);
        return $this->render('user/profil.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/search', name: 'app_user_search')]
    public function searchUser(Request $request, UserRepository $repository): Response
    {
        $query = $request->request->get('query');
        $users = $repository->searchByNom($query);
        return $this->render('user/search.html.twig', [
            'users' => $users
        ]);
    }

    public function displaySortedByIdASC(Request $request)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findBy([], ['id' => 'ASC']);
        
        return $this->render('user/admin.html.twig', [
            'users' => $users,
        ]);
    }

    public function displaySortedByNomASC(Request $request)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findBy([], ['nom' => 'ASC']);
        
        return $this->render('user/admin.html.twig', [
            'users' => $users,
        ]);
    }

    public function displaySortedByPrenomASC(Request $request)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findBy([], ['prenom' => 'ASC']);
        
        return $this->render('user/admin.html.twig', [
            'users' => $users,
        ]);
    }

    public function displaySortedByNumASC(Request $request)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findBy([], ['numtel' => 'ASC']);
        
        return $this->render('user/admin.html.twig', [
            'users' => $users,
        ]);
    }

    public function displaySortedByIdDESC(Request $request)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findBy([], ['id' => 'DESC']);
        
        return $this->render('user/admin.html.twig', [
            'users' => $users,
        ]);
    }

    public function displaySortedByNomDESC(Request $request)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findBy([], ['nom' => 'DESC']);
        
        return $this->render('user/admin.html.twig', [
            'users' => $users,
        ]);
    }

    public function displaySortedByPrenomDESC(Request $request)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findBy([], ['prenom' => 'DESC']);
        
        return $this->render('user/admin.html.twig', [
            'users' => $users,
        ]);
    }

    public function displaySortedByNumDESC(Request $request)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findBy([], ['numtel' => 'DESC']);
        
        return $this->render('user/admin.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('user/promote-to-admin/{id}', name: 'app_promote_to_admin')]
public function promoteToAdmin($id): RedirectResponse
{
    // Récupérez l'utilisateur à partir de l'identifiant
    $user = $this->getDoctrine()->getRepository(User::class)->find($id);

    // Mettez à jour le rôle de l'utilisateur en 'ADMIN'
    $user->setRole('ADMIN');

    // Enregistrez les modifications dans la base de données
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->flush();

    // Redirigez vers une page de confirmation ou une autre page appropriée
    return $this->redirectToRoute('app_user_back');
}

#[Route('user/demote-to-client/{id}', name: 'app_demote_to_client')]
public function demoteToClient($id): RedirectResponse
{
    // Récupérez l'utilisateur à partir de l'identifiant
    $user = $this->getDoctrine()->getRepository(User::class)->find($id);

    // Mettez à jour le rôle de l'utilisateur en 'ADMIN'
    $user->setRole('CLIENT');

    // Enregistrez les modifications dans la base de données
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->flush();

    // Redirigez vers une page de confirmation ou une autre page appropriée
    return $this->redirectToRoute('app_user_back');
}

#[Route('user/ban/{id}', name: 'app_user_ban')]
public function ban($id): RedirectResponse
{
    // Récupérez l'utilisateur à partir de l'identifiant
    $user = $this->getDoctrine()->getRepository(User::class)->find($id);

    // Mettez à jour le rôle de l'utilisateur en 'ADMIN'
    $user->setStatut('BANNED');

    // Enregistrez les modifications dans la base de données
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->flush();

    // Redirigez vers une page de confirmation ou une autre page appropriée
    return $this->redirectToRoute('app_user_back');
}

#[Route('user/active/{id}', name: 'app_user_active')]
public function active($id): RedirectResponse
{
    // Récupérez l'utilisateur à partir de l'identifiant
    $user = $this->getDoctrine()->getRepository(User::class)->find($id);

    // Mettez à jour le rôle de l'utilisateur en 'ADMIN'
    $user->setStatut('ACTIVE');

    // Enregistrez les modifications dans la base de données
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->flush();

    // Redirigez vers une page de confirmation ou une autre page appropriée
    return $this->redirectToRoute('app_user_back');
}

    #[Route('/user/stats', name: 'app_user_stat')]
    public function stats(UserRepository $userRepository)
    {
    $stats = $userRepository->getStatsByStatut();

    return $this->render('user/stats.html.twig', [
        'stats' => $stats,
    ]);
    }


}



