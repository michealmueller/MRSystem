<?php
    /**
     * Created by   PhpStorm.
     * User:        Micheal Mueller - MuellerTek
     * Web:         http://www.MuellerTek.com
     * Date:        12/13/2016
     * Time:        8:50 AM
     */

    session_start();

    require_once 'lib/html2pdf/vendor/autoload.php';
    if(isset($_GET['export']) && $_GET['export'] == 1) {


        preg_match_all('|(.*table-responsive">)([\s\S]*)|xi', $_SESSION['content'], $output);
        $content = '<html>
                <body><div class="container">'
            .$output[0][0];

        //echo '<div class="col-md-12" align="center"><a href="export.php?export=1"><button class="btn btn-warning">Export to PDF</button></a></div>';
        $_SESSION['content'] = $content;

        $html2pdf = new \Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'en');
        $html2pdf->setDefaultFont('courier');
        $html2pdf->writeHTML($_SESSION['content']);
        if($html2pdf->Output('Selected_Users.pdf','D') == true){
            header('Location: index.php');
        }
    }
    $_SESSION['content'] = preg_replace('|\<form name([\s\S]*)</form>|','', $_SESSION['content']);
    echo $_SESSION['content'];
    ?>
