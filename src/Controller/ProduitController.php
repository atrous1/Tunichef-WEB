<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\ResponseHeaderBag; // Add this for ResponseHeaderBag

use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/produit')]
class ProduitController extends AbstractController
{
    #[Route('/', name: 'app_produit_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $produits = $entityManager
            ->getRepository(Produit::class)
            ->findAll();

        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $imageFile */
            $imageFile = $form->get('imageProduit')->getData();

            // Check if a file was uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Generate a unique name for the file
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the desired directory
                $imageFile->move(
                    $this->getParameter('uploaded_images_directory'),
                    $newFilename
                );

                // Update the 'imageFilename' property of the produit entity
                $produit->setImageProduit($newFilename);
            }

            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }



#[Route('/export/excel', name: 'app_produit_export_excel')]
public function exportExcel(EntityManagerInterface $entityManager): Response
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Add headers
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Nom');
    $sheet->setCellValue('C1', 'Description');

    // Add more columns as needed

    // Fetch data
    $produits = $entityManager->getRepository(Produit::class)->findAll();

    // Fill data
    $row = 2;
    foreach ($produits as $produit) {
        $sheet->setCellValue('A'.$row, $produit->getIdProduit());
        $sheet->setCellValue('B'.$row, $produit->getNomProduit());
        $sheet->setCellValue('C'.$row, $produit->getDescriptionProduit());

        // Add more cells as needed
        $row++;
    }

    // Create Excel file
    $writer = new Xlsx($spreadsheet);
    $tempFileName = tempnam(sys_get_temp_dir(), 'excel');
    $writer->save($tempFileName);

    // Stream the file to the client
    return $this->file($tempFileName, 'produits.xlsx');

}
#[Route('/export/pdf', name: 'app_produit_export_pdf')]
public function exportPdf(EntityManagerInterface $entityManager): Response
{
    // Initialize Dompdf
    $dompdf = new Dompdf();
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $dompdf->setOptions($options);

    // Fetch data
    $produits = $entityManager->getRepository(Produit::class)->findAll();

    // HTML content for PDF
    $html = $this->renderView('produit/pdf.html.twig', [
        'produits' => $produits,
    ]);

    // Load HTML content
    $dompdf->loadHtml($html);

    // Render PDF
    $dompdf->render();

    // Stream the file to the client
    return new Response(
        $dompdf->output(),
        Response::HTTP_OK,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="produits.pdf"',
        ]
    );
}
    #[Route('/{idProduit}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{idProduit}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
{
    $OldImage = $produit->getImageProduit();
    $form = $this->createForm(ProduitType::class, $produit);
    
    // Set default image data only if $OldImage is not null
    if ($OldImage !== null) {
        echo $OldImage;
        $form->get('imageProduit')->setData($OldImage);
    }
   
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        /** @var Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile */
        $imageFile = $form->get('imageProduit')->getData();

        // Check if a new file was uploaded
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            // Generate a unique name for the file
            $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            // Move the file to the desired directory
            $imageFile->move(
                $this->getParameter('uploaded_images_directory'),
                $newFilename
            );

            // Update the 'imageFilename' property of the produit entity
            $produit->setImageProduit($newFilename);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('produit/edit.html.twig', [
        'produit' => $produit,
        'form' => $form,
    ]);
}

    
    #[Route('/{idProduit}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getIdProduit(), $request->request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }


}
