<?php

namespace App\Service;

use Spatie\PdfToText\Exceptions\PdfNotFound;
use Spatie\PdfToText\Pdf;

class ReadPdf
{
    /**
     * @throws PdfNotFound
     */
    public function extractPdfContent(string $pdfFilePath)
    {
        $pdf = new Pdf();
        // Chemin vers le fichier PDF
        $pdf->setPdf($pdfFilePath);

        return $pdf->text();
    }
}