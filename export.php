<?php
    /**
     * Created by   PhpStorm.
     * User:        Micheal Mueller - MuellerTek
     * Web:         http://www.MuellerTek.com
     * Date:        12/13/2016
     * Time:        8:50 AM
     */

    session_start();

    require 'vendor/autoload.php';
    use Dompdf\Dompdf;
    if(isset($_GET['export']) && $_GET['export'] == 1) {


        preg_match_all('|(.*table-responsive">)([\s\S]*)|xi', $_SESSION['content'], $output);
        $content = '<html>
                <body><div class="container">'
            .$output[0][0];

        //echo '<div class="col-md-12" align="center"><a href="export.php?export=1"><button class="btn btn-warning">Export to PDF</button></a></div>';
        //$_SESSION['content'] = $content;

        $dompdf = new Dompdf();
        $options = new \Dompdf\Options();
        $options->set('isHTML5ParserEnabled', true);
        $dompdf->loadHtml($content);
        $dompdf->setPaper('A4', 'Landscape');
        $dompdf->render();
        $pdf = $dompdf->output();
        $dompdf->stream(date('m-d-y_H:i:s').'-Selected.pdf');
        if(file_put_contents(date('m-d-y_H:i:s').'-Selected.pdf', $pdf)){
            header('Location: export.php');
        }
    }
    $_SESSION['content'] = preg_replace('|\<form name([\s\S]*)</form>|','', $_SESSION['content']);
    echo $_SESSION['content'];
    ?>
