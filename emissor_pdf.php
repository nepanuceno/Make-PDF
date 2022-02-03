<?php
$name = DIR_EPROC_TEMP_COMPARTILHADO . SessaoEproc::getInstance()->getNumIdUsuario() . '_relatorio.pdf';
//file_get_contents is standard function

if (file_exists($name)) {

    try {
        $content = file_get_contents($name);
    } catch (Exception $e) {
        echo $content = 'Exceção capturada: ',  $e->getMessage(), "\n";
    }

    header('Content-Type: application/pdf');
    header('Content-Length: ' . strlen($content));
    header('Content-disposition: attachment; filename="' . $name . '"');
    header('Cache-Control: public, must-revalidate, max-age=0');
    header('Pragma: public');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    echo $content;

    unlink($name);
    unlink(dirname(__FILE__). DIR_EPROC_TEMP_COMPARTILHADO . SessaoEproc::getInstance()->getNumIdUsuario() . '_relatorio.html');
}

