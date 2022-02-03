<?php
$pdf = new MakePDF(
    $strTitulo,
    [
        $strResultado,
        $_POST['txtDtaInicial'],
        $_POST['txtDtaFinal'],
        $filtros
    ],
    $css,
    isset($_POST['orientacao']) ? $_POST['orientacao'] : "Portrait",
    __DIR__ . "/../../imagens/mp_logo.png"
);

$pdf = $pdf->wkPDF(); //wkhtmltopdf

if ($pdf) {
    $url = SessaoEproc::getInstance()->assinarLink('controlador.php?acao=baixar_relatorio');
    $arrComandos[] = '<button type="button" accesskey="R" id="btnRelatorio" onclick="location.href=\''
        . $objPagina->formatarXHTML($url) . '\'; this.remove();" class="btn btn-danger ml-1"><i class="fas fa-file-pdf"></i>
        <span class="pl-2 infraTeclaAtalho">B</span>aixar Relatório</button>';
    $arrAux = $arrComandos[count($arrComandos) - 2];
    $arrComandos[count($arrComandos) - 2] = $arrComandos[count($arrComandos) - 1];
    $arrComandos[count($arrComandos) - 1] = $arrAux;
} else {
    $dictAlert = array('title' => 'Ops!', 'text' => 'Erro ao gerar documento PDF.', 'type' => 'error', 'autoExec' => true);
}
