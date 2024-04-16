<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;

class CommandeController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(SessionInterface $session, ProduitRepository $ProduitRepository): Response
    {
        $panier = $session->get("panier", []);

        $dataPanier = [];
        $total = 0;

        foreach($panier as $id => $quantite){
            $product = $ProduitRepository->find($id);
                $dataPanier[] = [
                    "produit" => $product,
                    "quantite" => $quantite
                ];
                $total += $product->getPrixProduit() * $quantite;
        }

        return $this->render('commande/panier.html.twig', compact("dataPanier", "total"));
    }

    #[Route('/add/{id}', name: 'PanierAdd')]
    public function add(int $id, SessionInterface $session): Response
    {
        $panier = $session->get("panier", []);

        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }

        $session->set("panier", $panier);

        return $this->redirectToRoute("app_panier");
    }

    #[Route('/remove/{id}', name: 'PanierRemove')]
    public function remove(int $id, SessionInterface $session): Response
    {
        $panier = $session->get("panier", []);

        if(!empty($panier[$id])){
            if($panier[$id] > 1){
                $panier[$id]--;
            }else{
                unset($panier[$id]);
            }
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("app_panier");
    }

    #[Route('/delete/{id}', name: 'PanierDelete')]
    public function delete(int $id, SessionInterface $session): Response
    {
        $panier = $session->get("panier", []);

        if(!empty($panier[$id])){
            unset($panier[$id]);
        }

        $session->set("panier", $panier);

        return $this->redirectToRoute("app_panier");
    }

    #[Route('/deleteAll', name: 'PanierDeleteAll')]
    public function deleteAll(SessionInterface $session): Response
    {
        $session->remove("panier");

        return $this->redirectToRoute("app_panier");   
    }

/*    #[Route('/commander', name: 'commander')]
    public function commander(SessionInterface $session): Response
    {
        $panier = $session->get("panier", []);

        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }

        $session->set("panier", $panier);

        return $this->redirectToRoute("app_panier");
    }*/
}
