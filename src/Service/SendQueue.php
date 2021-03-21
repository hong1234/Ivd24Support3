<?php
namespace App\Service;

use Twig\Environment;
use App\Dao\BaseDao;

class SendQueue
{
    private $bDao;
    private $twig;

    function __construct( BaseDao $bDao, Environment $twig) {
        $this->bDao = $bDao;
        $this->twig = $twig;
    }

    public function addToSendQueue($template,  $data){

        //$abc = 'ABC';
        //$xyz = 'XYZ';

        // $template = 'supporter/email.html.twig';
        // $data = [
        //     'abc' => $abc,
        //     'xyz' => $xyz 
        // ];

        //$tpl = $this->twig->render($template, $data);
        $tpl = $this->getHtmlCode($template, $data);
        //$file = $this->parameterBag->get('kernel.project_dir')."/zipfiles/people.txt";
        //file_put_contents($file, $tpl);
    }

    public function getHtmlCode($template,  $data=[]){
        return $this->twig->render($template, $data);
    }

}