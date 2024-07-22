<?php

use Mpdf\Mpdf;

class PdfService
{
    public function generatePdf($data)
    {
        $mpdf = new Mpdf([
            'default_font_size' => 8,
            'format' => 'A4', // Default page size is A4
            'orientation' => 'L', // Portrait orientation by default
            'default_font' => 'changa',
            'setAutoTopMargin' => 'stretch',
            'setAutoBottomMargin' => 'stretch',
        ]);
    }
}
