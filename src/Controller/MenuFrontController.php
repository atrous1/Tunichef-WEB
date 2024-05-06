<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Produit;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Writer\Result\PngResult;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Builder\BuilderInterface; 
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class MenuFrontController extends AbstractController
{   
    private $qrCodeBuilder;

    public function __construct(BuilderInterface  $qrCodeBuilder)
    {
        $this->qrCodeBuilder = $qrCodeBuilder;
    }

   
    #[Route('/front', name: 'app_front_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('front/menus.html.twig');
    }
    private function convertQrCodeResultToString(PngResult $qrCodeResult): string
    {
        return 'data:image/png;base64,' . base64_encode($qrCodeResult->getString());
    }
    #[Route('/kids', name: 'app_kids_index', methods: ['GET'])]
public function kids(MenuRepository $menuRepository, PaginatorInterface $paginator, Request $request): Response
{
    $menus = $menuRepository->findBy(['categorie' => 'Kids']);

    $products = [];

    // Générer les QR codes pour chaque produit
    foreach ($menus as $menu) {
        foreach ($menu->getProduits() as $product) {
            // Customize the QR code data (e.g., using product name)
            $qrCodeResult = $this->qrCodeBuilder
                ->data($product->getNomProduit())
                ->build();

            // Convert QR code result to string representation
            $qrCodeString = $this->convertQrCodeResultToString($qrCodeResult);

            // Ajouter le QR code à son produit correspondant
            $product->setQrCode($qrCodeString);

            // Ajouter le produit et son QR code au tableau
            $products[] = [
                'id' => $product->getIdProduit(),
                'nomProduit' => $product->getNomProduit(),
                'descriptionProduit' => $product->getDescriptionProduit(),
                'imageProduit' => $product->getImageProduit(),
                'prixProduit' => $product->getPrixProduit(),
                'qrCode' => $qrCodeString,
            ];
        }
    }

    // Paginer les résultats
    $pagination = $paginator->paginate(
        $products,
        $request->query->getInt('page', 1), // Numéro de page
        6 // Nombre d'éléments par page (2 lignes * 3 produits par ligne)
    );

    // Rendre la vue avec la pagination
    return $this->render('front/kids.html.twig', [
        'pagination' => $pagination,
    ]);
}

    
    
#[Route('/tunisian', name: 'app_tunisian_index', methods: ['GET'])]
public function tunisian(MenuRepository $menuRepository, PaginatorInterface $paginator, Request $request): Response
{
    // Récupérer tous les menus de la catégorie "Tunisian"
    $menus = $menuRepository->findBy(['categorie' => 'Tunisian']);

    $products = [];

    // Générer les QR codes pour chaque produit
    foreach ($menus as $menu) {
        foreach ($menu->getProduits() as $product) {
            // Customize the QR code data (e.g., using product name)
            $qrCodeResult = $this->qrCodeBuilder
                ->data($product->getNomProduit())
                ->build();

            // Convert QR code result to string representation
            $qrCodeString = $this->convertQrCodeResultToString($qrCodeResult);

            // Ajouter le QR code à son produit correspondant
            $product->setQrCode($qrCodeString);

            // Ajouter le produit et son QR code au tableau
            $products[] = [
                'id' => $product->getIdProduit(),
                'nomProduit' => $product->getNomProduit(),
                'descriptionProduit' => $product->getDescriptionProduit(),
                'imageProduit' => $product->getImageProduit(),
                'prixProduit' => $product->getPrixProduit(),
                'qrCode' => $qrCodeString,
            ];
        }
    }

    // Paginer les résultats
    $pagination = $paginator->paginate(
        $products,
        $request->query->getInt('page', 1), // Numéro de page
        6 // Nombre d'éléments par page (2 lignes * 3 produits par ligne)
    );

    // Rendre la vue avec la pagination
    return $this->render('front/tunisian.html.twig', [
        'pagination' => $pagination,
    ]);
}


#[Route('/european', name: 'app_european_index', methods: ['GET'])]
public function european(MenuRepository $menuRepository, PaginatorInterface $paginator, Request $request): Response
{
    // Récupérer tous les menus de la catégorie "European"
    $menus = $menuRepository->findBy(['categorie' => 'European']);

    $products = [];

    // Générer les QR codes pour chaque produit
    foreach ($menus as $menu) {
        foreach ($menu->getProduits() as $product) {
            // Customize the QR code data (e.g., using product name)
            $qrCodeResult = $this->qrCodeBuilder
                ->data($product->getNomProduit())
                ->build();

            // Convert QR code result to string representation
            $qrCodeString = $this->convertQrCodeResultToString($qrCodeResult);

            // Ajouter le QR code à son produit correspondant
            $product->setQrCode($qrCodeString);

            // Ajouter le produit et son QR code au tableau
            $products[] = [
                'id' => $product->getIdProduit(),
                'nomProduit' => $product->getNomProduit(),
                'descriptionProduit' => $product->getDescriptionProduit(),
                'imageProduit' => $product->getImageProduit(),
                'prixProduit' => $product->getPrixProduit(),
                'qrCode' => $qrCodeString,
            ];
        }
    }

    // Paginer les résultats
    $pagination = $paginator->paginate(
        $products,
        $request->query->getInt('page', 1), // Numéro de page
        6 // Nombre d'éléments par page (2 lignes * 3 produits par ligne)
    );

    // Rendre la vue avec la pagination
    return $this->render('front/european.html.twig', [
        'pagination' => $pagination,
    ]);
}

#[Route('/oriental', name: 'app_oriental_index', methods: ['GET'])]
public function oriental(MenuRepository $menuRepository, PaginatorInterface $paginator, Request $request): Response
{
    // Récupérer tous les menus de la catégorie "Oriental"
    $menus = $menuRepository->findBy(['categorie' => 'Oriental']);

    $products = [];

    // Générer les QR codes pour chaque produit
    foreach ($menus as $menu) {
        foreach ($menu->getProduits() as $product) {
            // Customize the QR code data (e.g., using product name)
            $qrCodeResult = $this->qrCodeBuilder
                ->data($product->getNomProduit())
                ->build();

            // Convert QR code result to string representation
            $qrCodeString = $this->convertQrCodeResultToString($qrCodeResult);

            // Ajouter le QR code à son produit correspondant
            $product->setQrCode($qrCodeString);

            // Ajouter le produit et son QR code au tableau
            $products[] = [
                'id' => $product->getIdProduit(),
                'nomProduit' => $product->getNomProduit(),
                'descriptionProduit' => $product->getDescriptionProduit(),
                'imageProduit' => $product->getImageProduit(),
                'prixProduit' => $product->getPrixProduit(),
                'qrCode' => $qrCodeString,
            ];
        }
    }

    // Paginer les résultats
    $pagination = $paginator->paginate(
        $products,
        $request->query->getInt('page', 1), // Numéro de page
        6 // Nombre d'éléments par page (2 lignes * 3 produits par ligne)
    );

    // Rendre la vue avec la pagination
    return $this->render('front/oriental.html.twig', [
        'pagination' => $pagination,
    ]);
}

#[Route('/front/detail/{id}', name: 'product_detail', methods: ['GET'])]
public function productDetail($id): Response
{
    // Récupérer le produit par son ID
    $product = $this->getDoctrine()->getRepository(Produit::class)->find($id);

    // Vérifier si le produit existe
    if (!$product) {
        throw $this->createNotFoundException('Product not found');
    }

    // Générer le QR code pour le produit
    $qrCodeResult = $this->qrCodeBuilder
        ->data($product->getNomProduit())
        ->build();

    // Convertir le résultat du QR code en chaîne
    $qrCodeString = $this->convertQrCodeResultToString($qrCodeResult);

    // Rendre la vue avec les détails du produit et le QR code
    return $this->render('front/detail.html.twig', [
        'product' => $product,
        'qrCode' => $qrCodeString,
    ]);
}


}
