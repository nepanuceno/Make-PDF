<?php

require_once dirname(__FILE__) . '/../../../../../web/Eproc.php';
require_once dirname(__FILE__) . '/../../../../../web/view/documento/DocumentoUtil.php';

class MakePDF
{

    private $title;
    private $content;
    private $style;
    private $format;
    private $logo;
    private $path;
    private $user;
    private $user_id;
    private $data_inicial;
    private $data_final;
    private $filtros;

    public function __construct($title, $content, $style = '', $format = "Portrait", $logo = '')
    {
        $this->title = $title;
        $this->content = $content[0];
        $this->style = $style;
        $this->format = $format;
        $this->logo = $logo;
        $this->user_id = SessaoEproc::getInstance()->getNumIdUsuario();
        $this->path = DIR_EPROC_TEMP_COMPARTILHADO . $this->user_id . '_relatorio.html';
        $this->user = SessaoEproc::getInstance()->getStrNomeUsuario();
        $this->data_inicial = $content[1];
        $this->data_final = $content[2];
        $this->filtros = $content[3];
    }

    public function wkPDF()
    {
        $this->html(); //Gera arquivo html
        $resultfile = DIR_EPROC_TEMP_COMPARTILHADO . $this->user_id . '_relatorio.pdf';
        $file = $this->path;
        $params = '-d 300 -T 7 -R 14 -B 7 -L 14 --page-size A2 -O ' . $this->format . ' --footer-right "Pag. [page] de [toPage]" --footer-font-size 10 --enable-external-links ';

        $commandline = DocumentoUtil::getComandoHTMLToPDF($file, $resultfile, $params);

        try {
            system($commandline, $retval);
        } catch (Exception $e) {
            echo 'Exceção capturada: Erro ao gerar Relatório PDF - ',  $e->getMessage(), "\n";
        }

        if (!file_exists($resultfile)) {
            throw new Exception('Erro ao converter HTML para PDF. Comando: ' . $commandline . ' Erro: ' . $retval);
        }
        if (file_exists($file)) {
            unlink($file);
        }
        return $resultfile;
    }


    private function html()
    {
        $strHtmlHeader = "<header class=\"center\">";
        $strHtmlHeader .= "<img src=\"$this->logo\" alt=\"MPE-TO\">";
        $strHtmlHeader .= "<h1>$this->title</h1>";
        $strHtmlHeader .= "<div class=\"rigth\">
        <h4>Relatório emitido por $this->user em " . date('d/m/Y H:i:s') . "</h4>
        </div>";

        if (isset($this->filtros)) {
            $strHtmlHeader .= "<div class=\"left\">";
            $strHtmlHeader .= "<h4>Filtros utilizados</h4>";
            if($this->data_inicial!=''){
                $strHtmlHeader .= "<span>Período:$this->data_inicial a $this->data_final</span>";
            }

            foreach ($this->filtros as $filtro) {
                if ($filtro != null && $filtro[1] != null && $filtro[1] != 'null') {
                    if ($filtro[1] == 'on') {
                        $strHtmlHeader .= "<div><strong>" . $filtro[0] . "</strong></div>" . PHP_EOL;
                    } else {
                        if($key >=1 && $this->filtros[$key-1][0] == $filtro[0]){
                            $strHtmlHeader .= "<div><span>" . $filtro[1] . "</span></div>" . PHP_EOL;
                        } else {
                            $strHtmlHeader .= "<div><strong>" . $filtro[0] . "</strong></div>" . PHP_EOL;
                            $strHtmlHeader .= "<div><span>" . $filtro[1] . "</span></div>" . PHP_EOL;
                        }
                    }
                }
            }
            $strHtmlHeader .= "</div>";
        }

        $strHtmlHeader .= "</header>";

        $strHTML = "<!doctype html>" . PHP_EOL;
        $strHTML .= "<html>" . PHP_EOL;
        $strHTML .= "<head>" . PHP_EOL;
        $strHTML .= "<style>" . $this->style . "</style>";
        $strHTML .= "</head>" . PHP_EOL;
        $strHTML .= "<body>" . PHP_EOL;
        $strHTML .= $strHtmlHeader . PHP_EOL;
        $strHTML .= $this->content;
        $strHTML .= "</body>" . PHP_EOL;
        $strHTML .= "</html>";

        try {
            file_put_contents($this->path, $strHTML);
        } catch (Exception $e) {
            echo 'Exceção capturada: Erro ao gerar HTML - ',  $e->getMessage(), "\n";
        }

        $file = $this->path;

        return ($strHTML);
    }

    private function slug($text)
    {
        $text = strtolower($text);
        $arr = array(' ', '/', '_', '?');
        $text = str_replace(' - ', '-', $text);
        $text = str_replace($arr, "-", $text);
        return $text;
    }
}
