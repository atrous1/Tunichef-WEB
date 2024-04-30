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
        // Convert the result to a string (e.g., base64 encode the image)
        // Adjust this logic based on how you want to represent the QR code data
        return 'data:image/png;base64,' . base64_encode($qrCodeResult->getString());
    }
    #[Route('/kids', name: 'app_kids_index', methods: ['GET'])]
    public function kids(MenuRepository $menuRepository): Response
    {
        // Get all menus with the category "Kids"
        $menus = $menuRepository->findBy(['categorie' => 'Kids']);

        $products = [];

        // Generate QR codes for each product
        foreach ($menus as $menu) {
            foreach ($menu->getProduits() as $product) {
                // Customize the QR code data (e.g., using product name)
                $qrCodeResult = $this->qrCodeBuilder
                    ->data($product->getNomProduit())
                    ->build();

                // Convert QR code result to string representation
                $qrCodeString = $this->convertQrCodeResultToString($qrCodeResult);

                // Add QR code string to the product entity
                $product->setQrCode($qrCodeString);

                // Add product details to the array
                $products[] = [
                    'nomProduit' => $product->getNomProduit(),
                    'descriptionProduit' => $product->getDescriptionProduit(),
                    'imageProduit' => $product->getImageProduit(),
                    'prixProduit' => $product->getPrixProduit(),
                    'qrCode' => $qrCodeString, // Include QR code in product details
                ];
            }
        }

        // Render the view with the products
        return $this->render('front/kids.html.twig', [
            'products' => $products,
        ]);
    }
    
    #[Route('/tunisian', name: 'app_tunisian_index', methods: ['GET'])]
    public function tunisian(): Response
    {
       // Get the Doctrine EntityManager
       $em = $this->getDoctrine()->getManager();

       // Get the menus with the category "Kids"
       $menus = $em->getRepository(Menu::class)->findBy(['categorie' => 'Tunisian']);

       // Array to store products
       $products = [];

       // Iterate through menus to fetch their associated products
       foreach ($menus as $menu) {
           // Get products associated with this menu
           $menuProducts = $menu->getProduits();

           // Append products to the products array
           foreach ($menuProducts as $product) {
               $products[] = [
                   'nomProduit' => $product->getNomProduit(),
                   'descriptionProduit' => $product->getDescriptionProduit(),
                   'imageProduit' => $product->getImageProduit(),
                   'prixProduit' => $product->getPrixProduit(),
                   
               ];
           }
       }
       return $this->render('front/tunisian.html.twig', [
           'products' => $products,
       ]);
    }

    #[Route('/european', name: 'app_european_index', methods: ['GET'])]
    public function european(): Response
    {
       $em = $this->getDoctrine()->getManager();

       $menus = $em->getRepository(Menu::class)->findBy(['categorie' => 'European']);

       $products = [];

       foreach ($menus as $menu) {
           $menuProducts = $menu->getProduits();

           foreach ($menuProducts as $product) {
               $products[] = [
                   'nomProduit' => $product->getNomProduit(),
                   'descriptionProduit' => $product->getDescriptionProduit(),
                   'imageProduit' => $product->getImageProduit(),
                   'prixProduit' => $product->getPrixProduit(),
               ];
           }
       }

       return $this->render('front/european.html.twig', [
           'products' => $products,
       ]);
    }

    #[Route('/oriental', name: 'app_oriental_index', methods: ['GET'])]
    public function oriental(): Response
    {
        // Get the Doctrine EntityManager
        $em = $this->getDoctrine()->getManager();

        // Get the menus with the category "Kids"
        $menus = $em->getRepository(Menu::class)->findBy(['categorie' => 'Oriental']);

        // Array to store products
        $products = [];

        // Iterate through menus to fetch their associated products
        foreach ($menus as $menu) {
            // Get products associated with this menu
            $menuProducts = $menu->getProduits();

            // Append products to the products array
            foreach ($menuProducts as $product) {
                $products[] = [
                    'nomProduit' => $product->getNomProduit(),
                    'descriptionProduit' => $product->getDescriptionProduit(),
                    'imageProduit' => $product->getImageProduit(),
                    'prixProduit' => $product->getPrixProduit(),
                    // Add other product properties as needed
                ];
            }
        }

        // Render the view with the products
        return $this->render('front/oriental.html.twig', [
            'products' => $products,
        ]);
    }
}
