<?php
namespace App\Service;  

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
//use App\Dao\UserDao;
use App\Dao\MaklerDao;

class MaklerService
{
    private $router;
    public $mDao;

    function __construct(UrlGeneratorInterface $router, MaklerDao $mDao) {
        $this->router = $router;
        $this->mDao = $mDao;
    }

    public function rand_str($length, $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'){
        $str = '';
        $count = strlen($charset);
        while ($length--) {
            $str .= $charset[mt_rand(0, $count-1)];
        }
        return $str;
    }

    public function newMakler($safePost){

        $anrede              = $safePost->get('anrede');
        $titel               = $safePost->get('titel');
        $namenstitel         = $safePost->get('namenstitel');

        $vorname             = $safePost->get('vorname');
        $name                = $safePost->get('name');
        $firma               = $safePost->get('firma');

        $strasse             = $safePost->get('strasse');
        $plz                 = $safePost->get('plz');
        $ort                 = $safePost->get('ort');
        $bundesland_id       = $safePost->get('bundesland');

        $telefon             = $safePost->get('telefon');
        $telefax             = $safePost->get('telefax');
            
        $mitgliedsnummer     = $safePost->get('mnummer');
        //$mkategorien       = $safePost->get('mkategorien'];

        $homepage            = $safePost->get('homepage');
	    $seo_url             = $safePost->get('seo_url');

        $regdate             = time();

        $benutzername        = $safePost->get('username');
        $email               = $safePost->get('email');
        $kennwort_plain      = $safePost->get('userpasswort');
        $kennwort            = md5($kennwort_plain);

        $ftppasswort         = $safePost->get('ftppasswort');
        $mySalt 			 = $this->rand_str(rand(100,200));
	    $ftppasswortcrypt	 = crypt($ftppasswort, $mySalt);

        $geschaeftsstelle_id = $safePost->get('geschaeftsstelle');

        $gs_werte = $this->mDao->getGeschaeftsstelle([
            'geschaeftsstelle_id' => $geschaeftsstelle_id
        ]);

        $bilderserver_id    = $gs_werte['bilderserver_id'];
        $ftp_server_id      = $gs_werte['ftp_server_id'];
        $move_robot_id      = $gs_werte['move_robot_id'];
        //$import_robot_id    = $gs_werte['import_robot_id'];

        $em = $this->mDao->getEm();
        $em->getConnection()->beginTransaction();
        try {

            $this->mDao->insertAccountForMakler([
                'art_id'            => '2',
                'recht_id'          => '3',
                'kennwort'          => $kennwort,
                'benutzername'      => $benutzername,
                'email'             => $email,
                'regdate'           => $regdate,
                'authentifiziert'   => '1',
                'gesperrt'          => '0',
                'kennwort_plain'    => $kennwort_plain
            ]);

            $user_id = $em->getConnection()->lastInsertId();

            $bilderordner  = "b00".$bilderserver_id.$user_id;
            $ftp_benutzer  = "f00".$ftp_server_id.$user_id;
            $homeftp       = "/home/ftpuser/".$ftp_benutzer;
    
            $this->mDao->insertMakler([
                'user_id'               => $user_id,
                'mitgliedsnummer'       => $mitgliedsnummer,
                'm_konfig_id'           => '1',
                'geschaeftsstelle_id'   => $geschaeftsstelle_id,
                'anrede'                => $anrede,
                'titel'                 => $titel,
                'namenstitel'           => $namenstitel,
                'name'                  => $name,
                'vorname'               => $vorname,
                'firma'                 => $firma,
                'strasse'               => $strasse,
                'plz'                   => $plz,
                'ort'                   => $ort,
                'geodb_laender_id'      => '60',
                'email'                 => $email,
                'telefon'               => $telefon,
                'telefax'               => $telefax,
                'homepage'              => $homepage,
                'seo_url'               => $seo_url,
                'sortierung'            => '1',
                'bundesland_id'         => $bundesland_id
                // 'mkategorien'           => $mkategorien
            ]);
   
            $this->mDao->insertUserMaklerConfig([
                'user_id'         => $user_id,
                'bilderserver_id' => $bilderserver_id,
                'bilderordner'    => $bilderordner,
                'ftp_server_id'   => $ftp_server_id,
                'ftp_benutzer'    => $ftp_benutzer,
                'ftppasswort'     => $ftppasswort,
                'move_robot_id'   => $move_robot_id,
            ]);

            $this->mDao->insertRobotQueue([
                'homeftp'           => $homeftp,
                'user_id'           => $user_id,
                'ftppasswortcrypt'  => $ftppasswortcrypt,
                'ftp_benutzer'      => $ftp_benutzer
            ]);
  
            $this->mDao->updateMaklerAhuGeoPoint([
                'user_id' => $user_id
            ]);

            //------------
            $em->getConnection()->commit();     

        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
    }

    public function maklerEdit($user_id, $safePost){

        $email          = $safePost->get('email');
        $anrede         = $safePost->get('anrede');
        $titel          = $safePost->get('titel');
        $namenstitel    = $safePost->get('namenstitel');
        $vorname        = $safePost->get('vorname');
        $name           = $safePost->get('name');
        $firma          = $safePost->get('firma');
        $strasse        = $safePost->get('strasse');
        $plz            = $safePost->get('plz');
        $ort            = $safePost->get('ort');
        $telefon        = $safePost->get('telefon');
        $telefax        = $safePost->get('telefax');
        $homepage       = $safePost->get('homepage');
        $mobil          = $safePost->get('mobil');
        $seo_url        = $safePost->get('seo_url');

        $em = $this->mDao->getEm();
        $em->getConnection()->beginTransaction();
        try {

            $this->mDao->updateUserAccountEmail([
                'email'     => $email,
                'user_id'   => $user_id
            ]);
    
            $this->mDao->updateMakler([
                'anrede'      => $anrede,
                'titel'       => $titel,
                'namenstitel' => $namenstitel,
                'name'        => $name,
                'vorname'     => $vorname,
                'firma'       => $firma,
                'strasse'     => $strasse,
                'plz'         => $plz,
                'ort'         => $ort,
                'email'       => $email,
                'telefon'     => $telefon,
                'telefax'     => $telefax,
                'homepage'    => $homepage,
                'seo_url'     => $seo_url,
                'mobil'       => $mobil,
                'user_id'     => $user_id
            ]);
         
            $em->getConnection()->commit();     

        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
    }

    public function maklerFtpPwEdit($user_id, $safePost){
        //$user_id        = $safePost->get('userid');
        $ftp_benutzer   = $safePost->get('ftp_user');
        $ftppasswort    = $safePost->get('ftppasswort');

        //srand ((double)microtime()*1000000);
        $mySalt = $this->rand_str(rand(100,200));
        $crypt_ftppasswort = crypt($ftppasswort, $mySalt);

        $em = $this->mDao->getEm();
        $em->getConnection()->beginTransaction();
        try {

            $this->mDao->updateUserMaklerConfig([
                'ftppasswort' => $ftppasswort,
                'user_id'     => $user_id
            ]);
        
            $this->mDao->insertRobotQueue2([
                'crypt_ftppasswort' => $crypt_ftppasswort,
                'ftp_benutzer'      => $ftp_benutzer
            ]);

            //-------------------
            $em->getConnection()->commit();

        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
    }

    public function deleteMakler($user_id){
        $user_makler = $this->mDao->getMaklerConfig(['user_id' => $user_id]);
    
        $bildpfad   =   $user_makler['bilderordner'];     // string 'b00111561' (length=9)
	    $bildserver =   $user_makler['bilderserver_id'];  // string '3' (length=1)
	    $ftppfad    =   $user_makler['ftp_benutzer'];     // string 'f00111561' (length=9)
	    $ftpserver  =   $user_makler['ftp_server_id'];    // string '5' (length=1)

        $stmt = $this->mDao->getObjectsByUserId(['user_id' => $user_id]);

        $em = $this->mDao->getEm();
        $em->getConnection()->beginTransaction();
        try {

            $this->mDao->insertUserDelete([
                'user_id'    => $user_id,
                'status'     => '1',
                'bildpfad'   => $bildpfad,
                'bildserver' => $bildserver,
                'ftppfad'    => $ftppfad,
                'ftpserver'  => $ftpserver
            ]);
        
            while ($row = $stmt->fetch()) {
                $objekt_id = $row['objekt_id'];
                $this->mDao->deleteAttachmentsByObjectId(['objekt_id' => $objekt_id]);
            }

            $this->mDao->deleteAllByUserId(['user_id' => $user_id]);
         
            //-------
            $em->getConnection()->commit();     

        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        } 
    }

    public function MaklerList(){

        $stmt = $this->mDao->getAllMakler();
        
        $rows = array();
        while ($row = $stmt->fetch()) {
            $row2 = array();
            $row2[] = $row['userId'];
            $row2[] = $row['vorname'].' '.$row['name'];
            $row2[] = $row['firma'];
            $row2[] = $row['maklerEmail'];
            $row2[] = $row['seo_url'];
            $row2[] = date("Y-m-d", (int)$row['registrierungsdatum']);
            $row2[] = date("Y-m-d", (int)$row['lastlogin']);
    
            $links = "<a href=".$this->router->generate('makler_edit', array('uid' => $row['userId'])).">Daten bearbeiten</><br>";
            $links = $links."<a href=".$this->router->generate('makler_ftp_edit', array('uid' => $row['userId'])).">FTP-Passwort bearbeiten</a><br>";
            $links = $links."<a href=".$this->router->generate('makler_pw_edit', array('uid' => $row['userId'])).">Passwort bearbeiten</a><br>";
            if($row['gesperrt'] == 1){
                $links = $links."<a href=".$this->router->generate('makler_lock_unlock', array('uid' => $row['userId'], 'gesperrt' => 0)).">Account entsperren</a><br>";
            } else {
                $links = $links."<a href=".$this->router->generate('makler_lock_unlock', array('uid' => $row['userId'], 'gesperrt' => 1)).">Account sperren</a><br>";
            }
            $row2[] = $links;

            $rows[] = $row2;
        }

        return $rows;
    }

    public function MaklerDelList(){

        $stmt = $this->mDao->getDelMakler();

        $rows = array();
        while ($row = $stmt->fetch()) {
            $tmp = array();
  
            $tmp[] = $row['user_id'];
            $tmp[] = $row['vorname'].' '.$row['name'];
            $tmp[] = $row['firma'];
            $tmp[] = $row['email'];
            $tmp[] = $row['mitgliedsnummer'];
            $tmp[] = substr($row['loesch_datum'], 0, 10);

            $link1 = "<a href=".$this->router->generate('makler_delete', array('uid' => $row['user_id'])).">Löschen</a><br>";
            $link2 = "<a href=".$this->router->generate('makler_delete_undo', array('uid' => $row['user_id'])).">Löschung zurücknehmen</a><br>";
            $tmp[] = $link1.$link2;
  
            $rows[] = $tmp;
        }

        return $rows;
    }

}