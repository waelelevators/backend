<?php

namespace App\Helpers;

use Mpdf\Mpdf;

class PdfHelper
{

    public static function generateContractPDF($name, $doneBy)
    {

        $mpdf = new Mpdf([
            'default_font_size' => 8,
            'default_font' => 'changa',
            'setAutoTopMargin' => 'stretch',
            'setAutoBottomMargin' => 'stretch',
        ]);

        // Set Header & Footer
        $mpdf->SetHTMLHeader('<div style="width:100%;height:30mm;padding-top:6mm"></div>');

        $mpdf->SetHTMLFooter('<div style="float:left;width:50%;height:30mm;">
            <h5 style="text-algin:center">{PAGENO}/{nbpg}</h5>
        </div>
        <div style="float:right;width:50%;height:30mm;">
            <h5>' . ($name ? $doneBy : '') . '</h5>
        </div>');


        $mpdf->SetDefaultBodyCSS('background', "url(https://waelsoft.com/cms/images/macca.png)");
        $mpdf->SetDefaultBodyCSS('background-image-resize', 6);

        return $mpdf;
    }
}
