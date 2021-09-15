<?php
namespace App\Service;  

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Dao\TemplateDao;

class TemplateService
{
    private $router;
    private $templateDao;

    function __construct(UrlGeneratorInterface $router, TemplateDao $templateDao) {
        $this->router = $router;
        $this->templateDao = $templateDao;
    }

    public function templatesByCategory2(){

        // $stmt = $this->templateDao->getTemplateByCategory([
        //     'kategorie_id' => '2'
        // ]);

        $stmt = $this->templateDao->getAllTemplate();

        $rows = array();
        while ($row = $stmt->fetch()) {

            $row2 = array();
            $row2[] = (int)$row->mail_template_id;
            $row2[] = (int)$row->kategorie_id;
            $row2[] = $row->titel;
            if(strlen($row->document_path)){
                $row2[] = 'Ja';
            } else {
                $row2[] = 'Nein';
            }
            $row2[] = $row->insert_date;

            $links = "<a class='' href=".$this->router->generate('template_edit', array('tempid' => $row->mail_template_id)).">Template bearbeiten</a><br>";
            $links = $links."<a onclick='showConfirm(event)' href=".$this->router->generate('template_delete', array('tempid' => $row->mail_template_id)).">Template delete</a><br>";
            
            $row2[] = $links;

            $rows[] = $row2;
        }

        return $rows;
    }

    public function getTemplateById($tempid){
        $rs = $this->templateDao->getRowInTableByIdentifier2('send_mail_templates', [
            'mail_template_id' => $tempid
        ]);
        return $rs;
    }

    public function insertTemplate($templatename, $template, $dokument){
        $this->templateDao->insertTemplate([
            'templatename'  => $templatename,
            'template'      => $template,
            'document_path' => $dokument
        ]);
    }

    public function updateTemplate($template_id, $templatename, $template, $dokument){
        $this->templateDao->updateTemplate([
            'templatename'  => $templatename,
            'template'      => $template,
            'document_path' => $dokument,
            'template_id'   => $template_id
        ]);
    }
}