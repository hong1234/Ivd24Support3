<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TempFileHandler
{
    private $tempFilePath;
    
    public function __construct(ParameterBagInterface $parameterBag) {
        $appPath = $parameterBag->get('kernel.project_dir');
        $this->tempFilePath = $appPath."/zipfiles/temp.json";
    }

    public function setTempoContent($dataArr){
        $json_data = json_encode($dataArr);
        $rs = file_put_contents($this->tempFilePath, $json_data);
        return $rs;
    }

    public function getTempoContent() {
        $json = file_get_contents($this->tempFilePath);
        $dataArr = json_decode($json, true);
        return $dataArr;
    }
}