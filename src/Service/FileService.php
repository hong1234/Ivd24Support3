<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FileService
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

    //----------------FTP---------------
    public function getConnectToFtpServer($ftp_server, $ftp_user_name, $ftp_user_pass){

        if($ftp_server !='' && $ftp_user_name !='' && $ftp_user_pass != ''){
            try {
                $conn_id = ftp_connect($ftp_server);
                $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

                if ((!$conn_id) || (!$login_result)) {
                    // echo 'FTP connection has failed! Attempted to connect to '. $ftp_server. ' for user '.$ftp_user_name.'.';
                } else {
                    return $conn_id;
                }
            } catch (\Exception $e) {
                //echo 'Caught exception: ',  $e->getMessage(), "\n"; exit;
                // continue;
            }
        }

        return false;
    }
    
}