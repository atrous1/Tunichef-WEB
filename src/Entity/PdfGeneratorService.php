<?php
// src/Entity/PdfGeneratorService.php

namespace App\Entity;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGeneratorService
{
    private $dompdf;

    public function __construct(Dompdf $dompdf)
    {
        $this->dompdf = $dompdf;
    }

    public function generatePdfFromReclamations($reclamations)
    {
        // Générer le contenu HTML avec le logo et les réclamations
        $htmlContent = $this->generateHtmlContent($reclamations);

        // Charger le contenu HTML dans Dompdf
        $this->dompdf->loadHtml($htmlContent);

        // Rendre le PDF
        $this->dompdf->render();

        // Récupérer le contenu du PDF
        $pdfContent = $this->dompdf->output();

        return $pdfContent;
    }

    private function generateHtmlContent($reclamations)
    {
        // Récupérer le chemin du logo
        $imagePath = $this->container->get('router')->getContext()->getBaseUrl() . '/assets/images/LOGO.jpg';

        // Générer le contenu HTML avec le logo et les réclamations
        $htmlContent = '
            <html>
            <head>
                <style>
                    /* Ajoutez des styles CSS si nécessaire */
                </style>
            </head>
            <body>
                <img src="' . $imagePath . '" alt="Logo">
                <h1>Liste des Réclamations</h1>
                <table border="1">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Avis</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($reclamations as $reclamation) {
            // Ajoutez les données de chaque réclamation dans le tableau
            $htmlContent .= '
                <tr>
                    <td>' . $reclamation->getDescription() . '</td>
                    <td>' . $reclamation->getDate()->format('Y-m-d') . '</td>
                    <td>' . $reclamation->getStatut() . '</td>
                    <td>' . $reclamation->getAvis() . '</td>
                </tr>';
        }

        // Finalisez le contenu HTML
        $htmlContent .= '
                    </tbody>
                </table>
            </body>
            </html>';

        return $htmlContent;
    }
}
