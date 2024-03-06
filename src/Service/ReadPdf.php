<?php

namespace App\Service;

use Spatie\PdfToText\Pdf;

class ReadPdf
{
    public function extractPdfContent(string $pdfFilePath)
    {
        // Chemin vers le fichier PDF
        $pdf = new Pdf($pdfFilePath);

        // Extraction du texte du PDF
        $text = $pdf->text();

        return $text ;
    }
}