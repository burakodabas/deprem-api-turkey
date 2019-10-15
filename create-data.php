<?php

header('Content-Type: text/html; charset=utf-8');

require 'simple_html_dom.php';


$html = file_get_html('http://www.koeri.boun.edu.tr/scripts/lst6.asp');
$depremData = $html->find('pre', 0);
$depremData = $depremData->plaintext;

$kullanData = substr($depremData, '610', strlen($depremData)); // clear top junk

$currentYear = date('Y');
$depremArray = explode($currentYear, $kullanData); // read per line
$tumDepremler = array(); // create empty Array

foreach ($depremArray as $deprem) {
    $deprem = $currentYear . $deprem; // explode function clear year.. and repeat fill must.

    if (strlen($deprem) > 25) {
        // strlen control because REVIZE (2019...) include year. and no line but include date...

        $ilksel_index = strpos(substr($deprem, 71, strlen($deprem)), "İlksel");
        $revize_index = strpos(substr($deprem, 71, strlen($deprem)), "REVIZE");
        $yer_ismi = "";
        $tip = "";

        if ($revize_index) {
            $yer_ismi = substr($deprem, 70, $revize_index);
            $tip = substr($deprem, (71 + $revize_index), strlen($deprem));
        } else if ($ilksel_index) {
            $yer_ismi = substr($deprem, 71, $ilksel_index);
            $tip = substr($deprem, (71 + $ilksel_index), strlen($deprem));
        }

        //clear $yer_ismi & $tip from space
        $yer_ismi = rtrim($yer_ismi);
        $tip = str_replace('(', '', $tip);
        $tip = trim($tip);


        $deprem = str_replace('\0', '', $deprem);
        $deprem = str_replace(' ', '', $deprem);


        $tarih = substr($deprem, 0, 10);
        $saat = substr($deprem, 10, 8);
        $enlem = substr($deprem, 18, 7);
        $boylam = substr($deprem, 25, 7);


        if (strpos(substr($deprem, 32, 4), '-')) {
            $derinlik = substr($deprem, 32, 3);
            $siddet = substr($deprem, 38, 3);
        } else if (strpos(substr($deprem, 32, 5), '-')) {
            $derinlik = substr($deprem, 32, 4);
            $siddet = substr($deprem, 39, 3);
        } else if (strpos(substr($deprem, 32, 6), '-')) {
            $derinlik = substr($deprem, 32, 5);
            $siddet = substr($deprem, 40, 3);
        } else {
            $derinlik = "bilinmiyor";
            $siddet = "bilinmiyor";
        }

        $depremSingle = array(
            'tarih' => $tarih,
            'saat' => $saat,
            'enlem' => $enlem,
            'boylam' => $boylam,
            'derinlik' => $derinlik,
            'siddet' => $siddet,
            'yer' => $yer_ismi,
            'tip' => $tip
        );
        $depremToken = md5($tarih . $saat . $enlem);
        array_push($tumDepremler, $depremSingle);


    }
}
echo json_encode($tumDepremler, true);
