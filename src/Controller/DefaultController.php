<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\HttpFoundation\Request;
// use App\Repository\ProductRepository;

use App\Dao\ServerDao;

/**
 *
 * @Route(path="")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/home", name="default_home")
     */
    public function homePage()
    {
        // return $this->json([
        //     'message' => 'Welcome to your new controller!',
        //     'path' => 'src/Controller/DefaultController.php',
        // ]);

        return $this->render('default/home.html.twig', [
            
        ]);
    }

    /**
     * @Route("/find", name="default_find")
     */
    public function findPage(ServerDao $sDao)
    {
      
        $stmt = $sDao->getAllServerConfig(); 

        $i =0;
        $beginn = 1601;
        $end    = 1650;
        while ($row=$stmt->fetchAssociative()) {

            $i++;

            if($i < $beginn){
                continue;
            }

            $ftp_server     = $row['hostname'];
            $ftp_user_name  = $row['ftp_benutzer'];
            $ftp_user_pass  = $row['ftp_passwort'];

            $conn_id = ftp_connect($ftp_server);
            
            try {
                $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
            } catch (\Exception $e) {
                //echo 'Caught exception: ',  $e->getMessage(), "\n"; exit;
                continue;
            }
        
            //$newcontents = ftp_chdir($conn_id, '/');
            $file_list = ftp_nlist($conn_id, ".");
            if($file_list !=null && count($file_list)>0){
                echo $row['user_id']." / ".$ftp_server." / ".$ftp_user_name; 
                exit;
            }

           
            if($i > $end){
                echo "nichts gefunden";
                exit;
            } 
        
        }

        return $this->render('default/test.html.twig', [
            
        ]);
    }

    /**
     * @Route("/test", name="default_test")
     */
    public function testPage(ServerDao $sDao)
    {

        $user_id = 10169;//11408;//10346 ;//105081;//11408;//10346 ;//19003;

        $makler_server  = $sDao->getServerOfMakler([
            'user_id' => $user_id
        ]);
        //var_dump($makler_server); exit;

        // 'user_id' => string '10071' 
        // 'name' => string 'Baumann' 
        // 'vorname' => string 'Helmut' 
        // 'firma' => string 'Baumann Immobilien'
        // 'email' => string 'info@immobaumann.de'

        // 'hostname' =>  'ftp001.ivd24immobilien.de' 
        // 'ftp_benutzer' =>  'f00210071'  => /home/ftpuser/f00210071 = zip-files depot von user "f00210071"
        // 'ftp_passwort' =>  '269zt5Q6' 

        // 'ftp_pause' => string 'N'
        // 'ftp_import_after_break' => string '0'

        //$user_id = $makler_server['user_id'];
        $ftp_server     = $makler_server['hostname'];
        $ftp_user_name = $makler_server['ftp_benutzer'];
        $ftp_user_pass = $makler_server['ftp_passwort'];


        $conn_id = ftp_connect($ftp_server);
        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

        if ((!$conn_id) || (!$login_result)) {
            echo 'FTP connection has failed! Attempted to connect to '. $ftp_server. ' for user '.$ftp_user_name.'.';
        } else {

            //$newcontents = ftp_chdir($conn_id, '/');
            $file_list = ftp_nlist($conn_id, ".");  // all file(namen)----------
            //var_dump($file_list); exit;

            if($file_list !=null && count($file_list)>0){

                //echo $user_id." / ".$ftp_server." / ".$ftp_user_name;
                //var_dump($file_list);
                
                // array (size=492)
                    //     0 => string 'IVD24-10346-20210129170420.xml' (length=30)
                    //     1 => string '88_kurz_eingangsseite.jpg' (length=25)
                    //     2 => string '111_lang3_balkon_mit_aussicht.jpg' (length=33)

                //exit;

                $sum = "";
                foreach($file_list as $item) {
                    $sum = $sum."<a href=".$this->generateUrl('default_test_download', array('uid' => $user_id, 'file' => str_replace(".","HH1z2Z7",$item))).">$item</a><br>";
                }
                echo $sum;

                exit;

            } else {
                echo "keine datei gefunden";
                exit;
            }

            // down load------------
            // ftp_pasv($conn_id, true);

            // $appPath = $this->getParameter('kernel.project_dir');  // 'C:\PHPtest\IVD24\BCLUB3\Ivd24Support2'

            // $localFilePath = $appPath."/zipfiles";

            // $remoteFilePath = $file_list[0];

            // // do down load
            // if (ftp_get($conn_id, $localFilePath."/$remoteFilePath", $remoteFilePath, FTP_BINARY)) {
                
            // } else {
            //     echo "fail ... ";
            //     exit;
            // }

        }

        //add your logic here 

        // return $this->json([
        //     'message' => 'Welcome to your new controller!',
        //     'path' => 'src/Controller/DefaultController.php',
        // ]);

        return $this->render('default/test.html.twig', [
            
        ]);
    } /////////////

    /**
     * @Route("/test2/{uid}/download/{file}", name="default_test_download")
     */
    public function testDownloadPage($uid, $file, ServerDao $sDao)
    {
        
        $user_id = $uid ; //10418; //----------------------------
        $remoteFilePath = str_replace("HH1z2Z7", ".", $file);

        $makler_server  = $sDao->getServerOfMakler([
            'user_id' => $user_id
        ]);

        // 'ftp_pause' => string 'N'
        // 'ftp_import_after_break' => string '0'

        //$user_id = $makler_server['user_id'];
        $ftp_server     = $makler_server['hostname'];
        $ftp_user_name  = $makler_server['ftp_benutzer'];
        $ftp_user_pass  = $makler_server['ftp_passwort'];


        $conn_id = ftp_connect($ftp_server);
        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

        if ((!$conn_id) || (!$login_result)) {
            echo 'FTP connection has failed! Attempted to connect to '. $ftp_server. ' for user '.$ftp_user_name.'.';
        } else {

            // down load------------
            ftp_pasv($conn_id, true);

            $appPath = $this->getParameter('kernel.project_dir');  // 'C:\PHPtest\IVD24\BCLUB3\Ivd24Support2'

            $localFilePath = $appPath."/zipfiles";

            //$remoteFilePath = $file_list[0];
            
            // do down load
            if (ftp_get($conn_id, $localFilePath."/$remoteFilePath", $remoteFilePath, FTP_BINARY)) {
                return $this->redirectToRoute('default_test', [
                    //'paramName' => 'value'
                ]);
            } else {
                echo "fail ... ";
                exit;
            }

        }

        // return $this->render('default/test.html.twig', [
            
        // ]);

    }

}