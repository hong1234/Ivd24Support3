<?php
namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Dao\ServerDao;

class FtpServer
{
    private $sDao;
    private $router;
    private $parameterBag;

    function __construct(UrlGeneratorInterface $router, ServerDao $sDao, ParameterBagInterface $parameterBag) {
        $this->router = $router; 
        $this->sDao = $sDao;
        $this->parameterBag = $parameterBag;
    }

    public function FtpPauseList(){
        $stmt = $this->sDao->getAllFtpServerPause();

        $rows = array();
        while ($row = $stmt->fetch()) {
            $row2 = array();

            $row2[] = $row['user_id'];
            //$row2[] = $row['vorname']." ".$row['name'];
            $row2[] = $row['firma'];
            $row2[] = $row['email'];
            
            $row2[] = $row['hostname'];      
            $row2[] = $row['ftp_benutzer'];  
            //$row2[] = $row['ftp_passwort']; 
            
            //$row2[] = $row['ftp_pause'];                //  => string 'N' 
            //$row2[] = $row['ftp_import_after_break'];   //  => string '0'
            if($row['ftp_import_after_break']=='1'){
                $row2[] = "<p style='color:red;'>import after break</p>";
            } else {
                $row2[] = "";
            }

            $str1   = "<a href=".$this->router->generate('server_starten', array('uid' => $row['user_id']))."><b>FTP-Import starten</b></a><br><br>";
            if($row['ftp_import_after_break']=='1'){
                
                $rs = $this->linksToFilesOnFTP($row['user_id']);
                
                $str2   = $rs[0];
                $str3   = $rs[1];
                $row2[] = $str1.$str2.$str3;
            } else {
                $row2[] = $str1;
            }
            
            $rows[] = $row2;
        }

        return $rows;
    }

    public function FtpList(){

        $stmt = $this->sDao->getAllServerConfig();

        $rows = array();
        while ($row = $stmt->fetch()) {
            $row2 = array();

            $row2[] = $row['user_id'];
            $row2[] = $row['vorname']." ".$row['name'];
            $row2[] = $row['firma'];
            $row2[] = $row['email'];
        
            $row2[] = $row['hostname'];                 // string 'ftp001.ivd24immobilien.de'
            $row2[] = $row['ftp_benutzer'];             //  => string 'testuser' (length=8)
            //$row2[] = $row['ftp_passwort'];           //  => string 'Test1234§' (length=10)
            
            //$row2[] = $row['ftp_pause'];              //  => string 'N' 
            //$row2[] = $row['ftp_import_after_break']; //  => string '0' (length=1)
            
            $row2[] = "<a href=".$this->router->generate('server_edit', array('uid' => $row['user_id'])).">FTP-Import pausieren</a><br>";  

            $rows[] = $row2;
        }
        return $rows;
    }

    public function linksToFilesOnFTP($user_id)
    {
        $links  = "";
        $links2 = "";
        $file_list = $this->getFilesOnFTP($user_id);
        
        if($file_list !=null && count($file_list)>0){
            $co = 0;
            foreach($file_list as $item) {
                $co++;
                if($co > 40) break;
                $links = $links."<a style='color:blue;' href=".$this->router->generate('server_download_file', array('uid' => $user_id, 'file' => str_replace(".","HH1z2Z7",$item))).">Download- $item</a><br>";
                $links2 = $links2."<a style='color:red;' class='delete-file' href=".$this->router->generate('server_delete_file', array('uid' => $user_id, 'file' => str_replace(".","HH1z2Z7",$item))).">Delete- $item</a><br>";
            }
        } 
        return [$links, $links2];
    }

    public function getFilesOnFTP($user_id)
    {
        $file_list = [];
        $conn_id = $this->getConnectToFtpServer($user_id);
        if(!$conn_id){
        } else {
            //$newcontents = ftp_chdir($conn_id, '/');
            $file_list = ftp_nlist($conn_id, ".");  // all file(namen)
            ftp_close($conn_id);
        }
        
        return $file_list;
    } 

    public function fileDownloadToApp($user_id, $file){
        $downloaded_file = "";
        $conn_id = $this->getConnectToFtpServer($user_id);
        if(!$conn_id){
        } else {
            $remoteFilePath = str_replace("HH1z2Z7", ".", $file);
            //$appPath = $this->getParameter('kernel.project_dir');  
            $appPath = $this->parameterBag->get('kernel.project_dir');// 'C:\PHPtest\IVD24\BCLUB3\Ivd24Support2'
            $localFilePath = $appPath."/zipfiles";

            ftp_pasv($conn_id, true);
            // do down load
            if(ftp_get($conn_id, $localFilePath."/$remoteFilePath", $remoteFilePath, FTP_BINARY)){
                $downloaded_file = $localFilePath."/$remoteFilePath";
            }
            
            ftp_close($conn_id);
        }
        return $downloaded_file;
    }

    public function deleteFileOnFtpServer($user_id, $file){

        $conn_id = $this->getConnectToFtpServer($user_id);
        if(!$conn_id){
        } else {
            $remoteFilePath = str_replace("HH1z2Z7", ".", $file);
            ftp_delete($conn_id, $remoteFilePath);
            ftp_close($conn_id);
        }
    }

    
    public function getConnectToFtpServer($user_id){
        
        $makler_server  = $this->sDao->getServerOfMakler([
            'user_id' => $user_id
        ]);

        $ftp_server     = $makler_server['hostname'];        // 'hostname' =>  'ftp001.ivd24immobilien.de' 
        $ftp_user_name  = $makler_server['ftp_benutzer'];    // 'ftp_benutzer' =>  'f00210071'  => /home/ftpuser/f00210071 = zip-files depot von user "f00210071"
        $ftp_user_pass  = $makler_server['ftp_passwort'];

        $conn_id = false;

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

        return $conn_id;
    }

}