<?php

namespace FastAdmin\lib\libraries;

use Dompdf\Dompdf;
use Dompdf\Options;
use LogicException;

class PdfWriter
{
    /**
     * instanza dompdf
     * @var Dompdf\DomPdf
     */
    private $dompdf;

    private $cwd;

    public function __construct(Options $options = null)
    {         
        if(!$options){
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
        }
        
        $this->cwd    = getcwd();
        $this->dompdf = new Dompdf($options);
    }

    public function getInstance()
    {
        return $this->dompdf;
    }

    public function stream($view, $filename, array $data = [], array $options = [])
    {
        $this->loadHtml($view, $data);
        
        $this->dompdf->render();

        $this->dompdf->stream($filename, $options);
        exit;
    }

    public function toPdf($view, $filepath, array $data = [])
    {
        $this->loadHtml($view, $data);

        $pdf = $this->dompdf->render();
        
        if(!$pdf){
            throw new LogicException('Impossibile generare il file pdf dalla view '.$view);
        }

        if(!file_put_contents($filepath, $pdf)){
            throw new LogicException('Impossibile scrivere il file pdf in '.$filepath);
        }

        chdir($this->cwd);

        return true;
    }

    protected function loadHtml(string $view, array $data = [])
    {
        $content = fa_resource_render($view, $data);
        
        $this->dompdf->getOptions()->setChroot(WP_FA_BASE_PATH);
        chdir(WP_FA_BASE_PATH);

        // instantiate and use the dompdf class
        $this->dompdf->loadHtml($content);

        $this->dompdf->setPaper('A4', 'portrait');

        return $this;
    }
}