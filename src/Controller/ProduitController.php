<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitRepository;
use App\Entity\Produit;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit')]
    public function index(): Response
    {
        return $this->render('produit/index.html.twig', [
            'controller_name' => 'ProduitController',
        ]);
    }

    #[Route('/showAll', name: 'app_show')]
    public function show(ProduitRepository $ProduitRepository): Response
    {
        $produits = $ProduitRepository->findAll();
        return $this->render('produit/showProducts.html.twig', ['produits' => $produits]);
    }
}
