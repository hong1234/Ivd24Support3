<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
// use App\Repository\ProductRepository;

use App\Dao\MaklerDao;
use App\Dao\UserDao;

/**
 *
 * @Route(path="/support")
 */
class MaklerController extends AbstractController
{
    public function rand_str($length, $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'){
        $str = '';
        $count = strlen($charset);
        while ($length--) {
            $str .= $charset[mt_rand(0, $count-1)];
        }
        return $str;
    }

    /**
     * @Route("/makler/new", name="makler_new")
     */
    public function newMakler(Request $request, UserDao $uDao)
    {
        //if(isset($_POST['savebutton'])) { // savebutton: true
        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            // post parameters
            // $safePost = filter_input_array(INPUT_POST);
            // var_dump($safePost); exit;

            $safePost = $request->request;
        
            $regdate              = time();
            $kennwort_plain       = $safePost->get('userpasswort');
            $kennwort             = md5($kennwort_plain);
            $benutzername         = $safePost->get('username');
            $email                = $safePost->get('email');
            $geschaeftsstelle_id  = $safePost->get('geschaeftsstelle');
            $mitgliedsnummer      = $safePost->get('mnummer');
            //$mkategorien        = $safePost->get('mkategorien'];
            $anrede               = $safePost->get('anrede');
            $titel                = $safePost->get('titel');
            $namenstitel          = $safePost->get('namenstitel');
            $vorname              = $safePost->get('vorname');
            $name                 = $safePost->get('name');
            $firma                = $safePost->get('firma');
            $strasse              = $safePost->get('strasse');
            $plz                  = $safePost->get('plz');
            $ort                  = $safePost->get('ort');
            $telefon              = $safePost->get('telefon');
            $telefax              = $safePost->get('telefax');
            $homepage             = $safePost->get('homepage');
	        $seo_url              = $safePost->get('seo_url');
            $bundesland_id        = $safePost->get('bundesland');
            $ftppasswort          = $safePost->get('ftppasswort');

            $mySalt 			= $this->rand_str(rand(100,200));
	        $ftppasswortcrypt	= crypt($ftppasswort, $mySalt);

            $gs_werte = $uDao->getGeschaeftsstelle([
                'geschaeftsstelle_id' => $geschaeftsstelle_id
            ]);

            $bilderserver_id    = $gs_werte['bilderserver_id'];
            $ftp_server_id      = $gs_werte['ftp_server_id'];
            $move_robot_id      = $gs_werte['move_robot_id'];
            //$import_robot_id    = $gs_werte['import_robot_id'];

            $em = $uDao->getEm();
            $em->getConnection()->beginTransaction();
            try {

                $uDao->insertAccountForMakler([
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
    
                $uDao->insertMakler([
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
   
                $uDao->insertUserMaklerConfig([
                    'user_id'         => $user_id,
                    'bilderserver_id' => $bilderserver_id,
                    'bilderordner'    => $bilderordner,
                    'ftp_server_id'   => $ftp_server_id,
                    'ftp_benutzer'    => $ftp_benutzer,
                    'ftppasswort'     => $ftppasswort,
                    'move_robot_id'   => $move_robot_id,
                ]);

                $uDao->insertRobotQueue([
                    'homeftp'           => $homeftp,
                    'user_id'           => $user_id,
                    'ftppasswortcrypt'  => $ftppasswortcrypt,
                    'ftp_benutzer'      => $ftp_benutzer
                ]);
  
                $uDao->updateMaklerAhuGeoPoint([
                    'user_id' => $user_id
                ]);

                $em->getConnection()->commit();     

            } catch (\Exception $e) {
                $em->getConnection()->rollBack();
                throw $e;
            }

            return $this->redirectToRoute('makler_list', [
                //'paramName' => 'value'
            ]);
            
        }

        $stmt = $uDao->getBundeslaender();
        $Bundeslaender = $stmt->fetchAllAssociative();

        $stmt = $uDao->getAllGeschaeftsstelle();
        $geschaeftsstelle = $stmt->fetchAllAssociative();
        
        return $this->render('makler/new.html.twig', [
            'Bundeslaender' => $Bundeslaender,
            'Geschaeftsstelle' => $geschaeftsstelle
        ]);
    }

    /**
     * @Route("/makler/{uid}/lock/{gesperrt}", name="makler_lock_unlock")
     */
    public function maklerLock($uid, $gesperrt, UserDao $uDao){
        $user_id  = $uid;
        $uDao->updateUserAccountGesperrt([
            'gesperrt' => $gesperrt,
            'user_id'  => $user_id
        ]);

        return $this->redirectToRoute('makler_list', [
            //'paramName' => 'value'
        ]);
    }

    /**
     * @Route("/makler/{uid}/ftpedit", name="makler_ftp_edit", requirements={"uid"="\d+"})
     */
    public function maklerFtpEdit($uid, Request $request, MaklerDao $mDao)
    {
        $user_id = $uid;

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {

            // $safePost = filter_input_array(INPUT_POST);
            // var_dump($safePost); exit;

            $safePost = $request->request;
            
            $ftppasswort    = $safePost->get('ftppasswort');
            $ftp_benutzer   = $safePost->get('ftp_user');
            $user_id        = $safePost->get('userid');

            //srand ((double)microtime()*1000000);
            $mySalt = $this->rand_str(rand(100,200));
            $crypt_ftppasswort = crypt($ftppasswort, $mySalt);

            $em = $mDao->getEm();
            $em->getConnection()->beginTransaction();
            try {
                $mDao->updateUserMaklerConfig([
                    'ftppasswort' => $ftppasswort,
                    'user_id'     => $user_id
                ]);
        
                $mDao->insertRobotQueue2([
                    'crypt_ftppasswort' => $crypt_ftppasswort,
                    'ftp_benutzer'      => $ftp_benutzer
                ]);

                $em->getConnection()->commit();

            } catch (\Exception $e) {
                $em->getConnection()->rollBack();
                throw $e;
            }
        }

        $user_makler = $mDao->getMaklerConfig([
            'user_id' => $user_id
        ]);

        $username = $user_makler['ftp_benutzer'];

        return $this->render('makler/ftpedit.html.twig', [
            'user_id'    => $user_id,
            'username'   => $username
        ]);
    }

    /**
     * @Route("/makler/{uid}/pwedit", name="makler_pw_edit", requirements={"uid"="\d+"})
     */
    public function maklerPwEdit($uid, Request $request, UserDao $uDao)
    {
        $user_id = $uid;

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            // $safePost = filter_input_array(INPUT_POST);
            // var_dump($safePost); exit;

            $safePost = $request->request;
            $passwort   = $safePost->get('passwort');
            $crypt_passwort = md5($passwort);

            $uDao->updateUserAccountPW([
                'crypt_passwort' => $crypt_passwort,
                'user_id'        => $user_id
            ]); 
        }
        
        $user_makler = $uDao->getUserAccount([
            'user_id' => $user_id
        ]);
        $username = $user_makler['username']; 

        return $this->render('makler/pwedit.html.twig', [
            'user_id'    => $user_id,
            'username'   => $username
        ]);
    }

    /**
     * @Route("/makler/{uid}/edit", name="makler_edit", requirements={"uid"="\d+"})
     */
    public function maklerEdit($uid, Request $request, MaklerDao $mDao, UserDao $uDao)
    {
        $user_id = $uid;

        // if(isset($_POST['savebutton'])) { // savebutton: true
        if ($request->isMethod('POST') && $request->request->get('savebutton')) {

            //$safePost = filter_input_array(INPUT_POST);
            //var_dump($safePost); exit;
            // $email          = $safePost['email'];

            $safePost = $request->request;
            //$request->request->get('name');
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

            $em = $uDao->getEm();
            $em->getConnection()->beginTransaction();
            try {
                $uDao->updateUserAccountEmail([
                    'email'     => $email,
                    'user_id'   => $user_id
                ]);
    
                $mDao->updateMakler([
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
        
        $user_makler  = $mDao->getMakler([
            'user_id' => $user_id
        ]);
    
        $user_makler2  = $uDao->getUserAccount([
            'user_id' => $user_id
        ]);
        
        return $this->render('makler/edit.html.twig', [
            'user_id'  => $user_id,
            'makler'   => array_merge($user_makler, $user_makler2)
        ]);
    }

    /**
     * @Route("/makler", name="makler_list")
     */
    public function maklerList(MaklerDao $mDao)
    {
        $stmt = $mDao->getAllMakler();
        
        $rows = array();
        while ($row = $stmt->fetchAssociative()) {
            $row2 = array();
            $row2[] = $row['userId'];
            $row2[] = $row['vorname'].' '.$row['name'];
            $row2[] = $row['firma'];
            $row2[] = $row['maklerEmail'];
            $row2[] = date("Y-m-d", (int)$row['registrierungsdatum']);
            $row2[] = date("Y-m-d", (int)$row['lastlogin']);
        
            //$str1 = "<a href='/admin/makler/".$row['userId']."/edit'>Daten bearbeiten</a><br>";
            $str1 = "<a href=".$this->generateUrl('makler_edit', array('uid' => $row['userId'])).">Daten bearbeiten</><br>";
            $str2 = "<a href=".$this->generateUrl('makler_ftp_edit', array('uid' => $row['userId'])).">FTP-Passwort bearbeiten</a><br>";
            $str3 = "<a href=".$this->generateUrl('makler_pw_edit', array('uid' => $row['userId'])).">Passwort bearbeiten</a><br>";
            $str4 = "";
            if($row['gesperrt']==1){
                $str4 = "<a href=".$this->generateUrl('makler_lock_unlock', array('uid' => $row['userId'], 'gesperrt' => 0)).">Account entsperren</a><br>";
            } else {
                $str4 = "<a href=".$this->generateUrl('makler_lock_unlock', array('uid' => $row['userId'], 'gesperrt' => 1)).">Account sperren</a><br>";
            }
            $row2[] = $str1.$str2.$str3.$str4;

            $rows[] = $row2;
        }
        
        return $this->render('makler/list.html.twig', [
            'dataSet' => $rows
        ]);
    }

    //----------------

    /**
     * @Route("/makler/delete", name="makler_delete_list")
     */
    public function maklerDelList(MaklerDao $mDao)
    {
        $stmt = $mDao->getDelMakler();

        $rows = array();
        while ($row = $stmt->fetchAssociative()) {
            $tmp = array();
  
            $tmp[] = $row['user_id'];
            $tmp[] = $row['vorname'].' '.$row['name'];
            $tmp[] = $row['firma'];
            $tmp[] = $row['email'];
            $tmp[] = $row['mitgliedsnummer'];
            $tmp[] = substr($row['loesch_datum'], 0, 10);

            $str1 = "<a href=".$this->generateUrl('makler_delete', array('uid' => $row['user_id'])).">Löschen</a><br>";
            $str2 = "<a href=".$this->generateUrl('makler_delete_undo', array('uid' => $row['user_id'])).">Löschung zurücknehmen</a><br>";
            $tmp[] = $str1.$str2;
  
            $rows[] = $tmp;
        }

        return $this->render('makler/del.list.html.twig', [
            'dataSet' => $rows
        ]);
    }

    /**
     * @Route("/makler/{uid}/delete", name="makler_delete", requirements={"uid"="\d+"})
     */
    public function maklerDelete($uid, Request $request, MaklerDao $mDao, UserDao $uDao)
    {
        $user_id = $uid;

        if ($request->isMethod('POST')) {

            if ($request->request->get('savebutton')) {

                $user_makler = $mDao->getMaklerConfig(['user_id' => $user_id]);
    
                $bildpfad   =   $user_makler['bilderordner'];     // string 'b00111561' (length=9)
	            $bildserver =   $user_makler['bilderserver_id'];  // string '3' (length=1)
	            $ftppfad    =   $user_makler['ftp_benutzer'];     // string 'f00111561' (length=9)
	            $ftpserver  =   $user_makler['ftp_server_id'];    // string '5' (length=1)

                $stmt = $mDao->getObjectsByUserId(['user_id' => $user_id]);

                $em = $mDao->getEm();
                $em->getConnection()->beginTransaction();
                try {

                    $mDao->insertUserDelete([
                        'user_id'    => $user_id,
                        'status'     => '1',
                        'bildpfad'   => $bildpfad,
                        'bildserver' => $bildserver,
                        'ftppfad'    => $ftppfad,
                        'ftpserver'  => $ftpserver
                    ]);
        
                    while ($row = $stmt->fetchAssociative()) {
                        $objekt_id = $row['objekt_id'];
                        $mDao->deleteAttachmentsByObjectId(['objekt_id' => $objekt_id]);
                    }

                    $mDao->deleteAllByUserId(['user_id' => $user_id]);
         
                    $em->getConnection()->commit();     

                } catch (\Exception $e) {
                    $em->getConnection()->rollBack();
                    throw $e;
                }
            }

            return $this->redirectToRoute('makler_delete_list', [
                //'paramName' => 'value'
            ]);
                
        }

        $user_makler  = $mDao->getMakler([
            'user_id' => $user_id
        ]);
    
        $user_makler2  = $uDao->getUserAccount([
            'user_id' => $user_id
        ]); 
        
        return $this->render('makler/del.html.twig', [
            'user_id'     => $user_id,
            'makler' => array_merge($user_makler, $user_makler2)
        ]);

    }

    /**
     * @Route("/makler/{uid}/delete/undo", name="makler_delete_undo", requirements={"uid"="\d+"})
     */
    public function deleteUndo($uid, UserDao $uDao){
        $user_id = $uid;
        $uDao->updateUserAccountLoeschung([
            'user_id' => $user_id
        ]);

        return $this->redirectToRoute('makler_delete_list', [
            //'paramName' => 'value'
        ]);
    }

}