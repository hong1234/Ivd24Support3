<?php
namespace App\Service;  

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Dao\MaklerDao;
use App\Service\SendQueue;
use App\Service\StringFormat;

class MaklerService
{
    private $router;
    private $mDao;
    private $sqService;
    private $fmService;

    function __construct(UrlGeneratorInterface $router, MaklerDao $mDao, SendQueue $sqService, StringFormat $fmService) {
        $this->router = $router;
        $this->mDao = $mDao;
        $this->sqService = $sqService;
        $this->fmService = $fmService;
    }

    public function getMaklerData($user_id) {
        $user_account = $this->mDao->getRowInTableByIdentifier('user_account', ['user_id' => $user_id]);
        $username = $user_account['username'];
        $user_makler = $this->mDao->getRowInTableByIdentifier('user_makler', ['user_id' => $user_id]);
        $user_makler['username'] = $username;
        return $user_makler;
    }

    public function userMaklerConfig($geschaeftsstelle_id, $user_id) {
        $gs_werte = $this->mDao->getRowInTableByIdentifier('user_geschaeftsstelle', [
            'geschaeftsstelle_id' => $geschaeftsstelle_id
        ]);
        $bilderserver_id = $gs_werte['bilderserver_id'];
        $ftp_server_id   = $gs_werte['ftp_server_id'];
        $move_robot_id   = $gs_werte['move_robot_id'];
        $import_robot_id = $gs_werte['import_robot_id'];

        // $bilderordner = "b00".$geschaeftsstelle_id.$user_id;
        // $ftp_benutzer = "f00".$geschaeftsstelle_id.$user_id;

        $bilderordner = "b00".$bilderserver_id.$user_id;
        $ftp_benutzer = "f00".$ftp_server_id.$user_id;

        $homeftp = "/home/ftpuser/".$ftp_benutzer;

        return [
            'bilderordner'    => $bilderordner,
            'ftp_benutzer'    => $ftp_benutzer,
            'homeftp'         => $homeftp,
            'bilderserver_id' => $bilderserver_id,
            'ftp_server_id'   => $ftp_server_id,
            'move_robot_id'   => $move_robot_id,
            'import_robot_id' => $import_robot_id
        ];
    }

    public function newMakler($safePost) {

        $geschaeftsstelle_id = $safePost->get('geschaeftsstelle_id');
        $bundesland_id = $safePost->get('bundesland_id');
        $mkategorie_id = $safePost->get('mkategorie_id');

        $mitgliedsnummer = $safePost->get('mitgliedsnummer');
        $anrede  = $safePost->get('anrede');
        $titel   = $safePost->get('titel');
        $vorname = $safePost->get('vorname');
        $name    = $safePost->get('name');
        $strasse = $safePost->get('strasse');
        $plz     = $safePost->get('plz');
        $ort     = $safePost->get('ort');
        
        $firma    = $safePost->get('firma');
        $telefon  = $safePost->get('telefon');
        $telefax  = $safePost->get('telefax');
        $homepage = $safePost->get('homepage');
	    
        $username    = $safePost->get('username');
        $email       = $safePost->get('email');
        $passwort    = $safePost->get('passwort');
        $ftppasswort = $safePost->get('ftppasswort');
        $seo_url     = $safePost->get('seo_url');
        
        //---------
        $ftppasswortcrypt = $this->fmService->getPwCrypt($ftppasswort);

        $conn = $this->mDao->dbConnection();
        $conn->beginTransaction();
        try {

            $this->mDao->insertAccountForMakler([
                'md5_pw'   => md5($passwort),
                'username' => $username,
                'email'    => $email,
                'regdate'  => time()
                //'passwort' => $passwort
            ]);

            $user_id = $conn->lastInsertId();

            $userMaklerConfig = $this->userMaklerConfig($geschaeftsstelle_id, $user_id);
    
            $this->mDao->insertMakler([
                'user_id'         => $user_id,
                'geschaeftsstelle_id' => $geschaeftsstelle_id,
                'mitgliedsnummer' => $mitgliedsnummer,
                'mkategorie_id'   => $mkategorie_id,
                'anrede'          => $anrede,
                'titel'           => $titel,
                'name'            => $name,
                'vorname'         => $vorname,
                'firma'           => $firma,
                'strasse'         => $strasse,
                'plz'             => $plz,
                'ort'             => $ort,
                'email'           => $email,
                'telefon'         => $telefon,
                'telefax'         => $telefax,
                'homepage'        => $homepage,
                'seo_url'         => $seo_url,
                'bundesland_id'   => $bundesland_id
            ]);
   
            $this->mDao->insertUserMaklerConfig([
                'user_id'         => $user_id,
                'move_robot_id'   => $userMaklerConfig['move_robot_id'],
                'bilderserver_id' => $userMaklerConfig['bilderserver_id'],
                'bilderordner'    => $userMaklerConfig['bilderordner'],
                'ftp_server_id'   => $userMaklerConfig['ftp_server_id'],
                'ftp_benutzer'    => $userMaklerConfig['ftp_benutzer'],
                'ftppasswort'     => $ftppasswort,
                'returncode_businessclub' => $email
            ]);

            $this->mDao->insertRobotQueue([
                'homeftp'          => $userMaklerConfig['homeftp'],
                'user_id'          => $user_id,
                'ftppasswortcrypt' => $ftppasswortcrypt,
                'ftp_benutzer'     => $userMaklerConfig['ftp_benutzer']
            ]);
  
            $this->mDao->updateMaklerAhuGeoPoint([
                'user_id' => $user_id
            ]);

            $this->sqService->addToSendQueue('makler_new', [
                'username' => $username,
                'email'    => $email,
                'passwort' => $passwort
            ]);
            
            $conn->commit();     
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }

    }

    public function maklerEdit($user_id, $safePost) {

        $email    = $safePost->get('email');
        $seo_url  = $safePost->get('seo_url');

        $anrede   = $safePost->get('anrede');
        $titel    = $safePost->get('titel');
        $vorname  = $safePost->get('vorname');
        $name     = $safePost->get('name');
        
        $strasse  = $safePost->get('strasse');
        $ort      = $safePost->get('ort');
        $plz      = $safePost->get('plz');

        $firma    = $safePost->get('firma');
        $telefon  = $safePost->get('telefon');
        $telefax  = $safePost->get('telefax');
        $homepage = $safePost->get('homepage');
        $mobil    = $safePost->get('mobil');
        
        $conn = $this->mDao->dbConnection();
        $conn->beginTransaction();
        try {

            $this->mDao->updateUserAccountEmail([
                'email'   => $email,
                'user_id' => $user_id
            ]);
    
            $this->mDao->updateMakler([
                'anrede'   => $anrede,
                'titel'    => $titel,
                'name'     => $name,
                'vorname'  => $vorname,
                'firma'    => $firma,
                'strasse'  => $strasse,
                'plz'      => $plz,
                'ort'      => $ort,
                'email'    => $email,
                'telefon'  => $telefon,
                'telefax'  => $telefax,
                'homepage' => $homepage,
                'seo_url'  => $seo_url,
                'mobil'    => $mobil,
                'user_id'  => $user_id
            ]);
         
            $conn->commit();     
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function maklerFtpPwEdit($user_id, $safePost) {

        $user_makler = $this->mDao->getRowInTableByIdentifier('user_makler', [
            'user_id' => $user_id
        ]);
        $email = $user_makler['email'];

        //$user_id = $safePost->get('userid');
        $ftp_benutzer = $safePost->get('ftp_user');
        $ftppasswort  = $safePost->get('ftppasswort');
        $crypt_ftppasswort = $this->fmService->getPwCrypt($ftppasswort);

        $conn = $this->mDao->dbConnection();
        $conn->beginTransaction();
        try {

            $this->mDao->updateUserMaklerConfig([
                'ftppasswort' => $ftppasswort,
                'user_id'     => $user_id
            ]);
        
            $this->mDao->insertRobotQueue2([
                'crypt_ftppasswort' => $crypt_ftppasswort,
                'ftp_benutzer'      => $ftp_benutzer
            ]);

            $this->sqService->addToSendQueue('makler_edit_ftp_pw', [
                'email'    => $email,
                'passwort' => $ftppasswort
            ]);

            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function maklerPwEdit($user_id, $safePost){

        $user_makler = $this->mDao->getRowInTableByIdentifier('user_account', [
            'user_id' => $user_id
        ]);
        $email = $user_makler['email'];

        $passwort = $safePost->get('passwort');

        $conn = $this->mDao->dbConnection();
        $conn->beginTransaction();
        try {

            $this->mDao->updateUserAccountPW([
                'crypt_passwort' => md5($passwort),
                'user_id'        => $user_id
            ]); 

            $this->sqService->addToSendQueue('makler_edit_pw', [
                'email'    => $email,
                'passwort' => $passwort
            ]);

            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function insertUserDelete($user_id){
        $makler_config = $this->mDao->getRowInTableByIdentifier('user_makler_config', [
            'user_id' => $user_id
        ]);
    
        $bilderordner = $makler_config['bilderordner'];     // string 'b00111561' (length=9)
	    $bildserver   = $makler_config['bilderserver_id'];  // string '3' (length=1)
	    $ftp_benutzer = $makler_config['ftp_benutzer'];     // string 'f00111561' (length=9)
	    $ftpserver    = $makler_config['ftp_server_id'];    // string '5' (length=1)

        $this->mDao->insertUserDelete([
            'user_id'    => $user_id,
            'status'     => '1',
            'bildpfad'   => $bilderordner,
            'bildserver' => $bildserver,
            'ftppfad'    => $ftp_benutzer,
            'ftpserver'  => $ftpserver
        ]);
    }

    public function deleteMakler($user_id) {

        $stmt = $this->mDao->getObjectsByUserId(['user_id' => $user_id]);
        $conn = $this->mDao->dbConnection();
        $conn->beginTransaction();
        try {
            //$this->insertUserDelete($user_id);

            while ($row = $stmt->fetch()) {
                $objekt_id = $row['objekt_id'];
                $this->mDao->deleteAttachmentsByObjectId(['objekt_id' => $objekt_id]);
            }

            $this->mDao->deleteAllByUserId(['user_id' => $user_id]);
         
            $conn->commit();     
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        } 
    }

    public function MaklerList(){

        $stmt = $this->mDao->getAllMakler();
        
        $rows = array();
        while ($row = $stmt->fetch()) {
            $row2 = array();
            // $row2[] = "<a id='".$row['userId']."' onclick='moreInfo(this, ".$row['userId'].")'>".$row['userId']."</a>";
            $row2[] = $row['userId'];
            $row2[] = $row['mitgliedsnummer'];
            $row2[] = $row['vorname'].' '.$row['name'];
            $row2[] = $row['firma'];
            $row2[] = "<a href = 'mailto: ".$row['maklerEmail']."'>".$row['maklerEmail']."</a>" ;
            // $row2[] = $row['seo_url'];
            $row2[] = $row['ort'];
            // $row2[] = date("Y-m-d", (int)$row['registrierungsdatum']);
            // $row2[] = date("Y-m-d", (int)$row['lastlogin']);

            $row2[] = $row['reg_date'];
            $row2[] = $row['last_login'];
            
            $links = "<a class='m-link-button' id='".$row['userId']."' onclick='moreInfo(this, ".$row['userId'].")'>Weitere Infos</a><br><br>";
            $links = $links."<a class='m-link-button' href=".$this->router->generate('makler_edit', array('uid' => $row['userId'])).">Daten bearbeiten</a><br><br>";
            $links = $links."<a class='m-link-button' href=".$this->router->generate('makler_ftp_edit', array('uid' => $row['userId'])).">FTP-Passwort bearbeiten</a><br><br>";
            $links = $links."<a class='m-link-button' href=".$this->router->generate('makler_pw_edit', array('uid' => $row['userId'])).">Passwort bearbeiten</a><br><br>";
            if($row['gesperrt'] == 1){
                $links = $links."<a class='m-link-button' href=".$this->router->generate('makler_unlock', array('uid' => $row['userId'])).">Account entsperren</a><br><br>";
            } else {
                $links = $links."<a class='m-link-button' href=".$this->router->generate('makler_lock', array('uid' => $row['userId'])).">Account sperren</a><br><br>";
            }
            if($row['loeschung'] == 0){
                $links = $links."<a class='m-link-button' class='into_delete-list' href=".$this->router->generate('makler_into_delete', array('uid' => $row['userId'])).">In Delete-List schieben</a><br><br>";
            }

            $links = $links."<a class='m-link-button' href='https://ivd24immobilien.de/wp-admin/admin.php?page=ivd24Admin_show&id=".$row['userId']."&art=5' target='_blank'>Login als Makler</a><br>";
            
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
            $tmp[] = $row['mitgliedsnummer'];
            $tmp[] = $row['vorname'].' '.$row['name'];
            $tmp[] = $row['firma'];
            $tmp[] = $row['email'];
            // $tmp[] = substr($row['loesch_datum'], 0, 10);
            $tmp[] = $row['loesch_datum'];

            $link1 = "<a href=".$this->router->generate('makler_delete', array('uid' => $row['user_id'])).">Löschen</a><br>";
            $link2 = "<a href=".$this->router->generate('makler_delete_undo', array('uid' => $row['user_id'])).">Löschung zurücknehmen</a><br>";
            $tmp[] = $link1.$link2;
  
            $rows[] = $tmp;
        }

        return $rows;
    }

}