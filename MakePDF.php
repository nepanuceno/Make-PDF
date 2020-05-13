<?php

class MakePDF
{

    private $title;
    private $content;
    private $style;
    private $format;
    private $logo;
    private $path;
    private $user;
    private $data_inicial;
    private $data_final;

    public function __construct($title, $content, $style='', $format="Portrait", $logo='')
    {
        $this->title = $title;
        $this->content = $content[0];
        $this->style = $style;
        $this->format = $format;
        $this->logo = $logo;
        $this->path = DIR_EPROC_TEMP_COMPARTILHADO.'relatorio.html';
        $this->user = SessaoEproc::getInstance()->getStrNomeUsuario();
        $this->data_inicial = $content[1];
        $this->data_final = $content[2];

    }

    public function wkPDF()
    {
        $this->html();
        $exec = "/../../libs/wkhtmltopdf-amd64 ";
        $resultfile = DIR_EPROC_TEMP_COMPARTILHADO.'relatorio.pdf';        
        $params = '-d 300 -T 7 -R 14 -B 7 -L 14 --page-size A2 -O '. $this->format.' --footer-right "Pag. [page] de [toPage]" --footer-font-size 10 ';
        $cmd=__DIR__.$exec.$params.' "'.$this->path.'" "'.$resultfile.'"';

        $output = shell_exec($cmd);        

        if(!$output){
            return true;
        } else {
            return false;
        }
    }


    private function html()
    {
        $strHtmlHeader = "<header class=\"center\">";
        $strHtmlHeader .= "<img src=\"$this->logo\" alt=\"MPE-TO\">";
        $strHtmlHeader .= "<h1>$this->title</h1>";
        $strHtmlHeader .= "<div class=\"rigth\">
        <h4>Relatório emitido por $this->user em ".date('d/m/Y H:i:s')."</h4>
        <span>Período:$this->data_inicial a $this->data_final</span>
        </div>";
        $strHtmlHeader .= "</header>";

        $strHTML = "<html>".PHP_EOL;
        $strHTML .= "<head>".PHP_EOL;
        $strHTML .= "<style>".$this->style."</style>";
        $strHTML .= "</head>".PHP_EOL;
        $strHTML .= "<body>".PHP_EOL;
        $strHTML .= $strHtmlHeader.PHP_EOL;
        $strHTML .= $this->content;
        $strHTML .= "</body>".PHP_EOL;
        $strHTML .= "</html>";
        
        file_put_contents($this->path, $strHTML);

        return ($strHTML);
    }

    private function slug($text)
    {
        $text = strtolower($text);
        $arr = array(' ','/','_','?');
        $text = str_replace(' - ','-', $text);
        $text = str_replace($arr,"-", $text);
        return $text;

    }

}
