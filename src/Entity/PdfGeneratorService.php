<?php
// src/Entity/PdfGeneratorService.php

namespace App\Entity;

use TCPDF;

class PdfGeneratorService
{
    public function generatePdf($reclamations)
    {
        // Création d'une nouvelle instance TCPDF
        $pdf = new TCPDF();
        
        // Ajout d'une nouvelle page au PDF
        $pdf->AddPage();
        
        // Écriture du contenu des réclamations dans le PDF
        foreach ($reclamations as $reclamation) {
            $pdf->writeHTML('<h1>Réclamation</h1>');
            $pdf->writeHTML('<p>Description: ' . $reclamation->getDescription() . '</p>');
            $pdf->writeHTML('<p>Avis: ' . $reclamation->getAvis() . '</p>');
            $pdf->writeHTML('<p>Staut: ' . $reclamation->getStatut() . '</p>');
            // Ajoutez d'autres champs de réclamation selon votre besoin
        }
        
        // Récupération du contenu du PDF
        $pdfContent = $pdf->Output('output.pdf', 'S');
        
        return $pdfContent;
    }
}

