<?php
namespace App\Service;  

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Dao\InteressentDao;

class InteressentService
{
    private $router;
    private $iDao;

    function __construct(UrlGeneratorInterface $router, InteressentDao $iDao) {
        $this->router = $router;
        $this->iDao = $iDao;
    }

    public function getInteressentData($user_id) {
        $user_interessent  = $this->iDao->getRowInTableByIdentifier('user_interessent', ['user_id' => $user_id]);
        $user_account = $this->iDao->getRowInTableByIdentifier('user_account', ['user_id' => $user_id]);
        $username = $user_account['username'];
        $user_interessent['username'] = $username;
        return $user_interessent;
    }

    public function interessentUpdate($user_id, $safePost){

        $email       = $safePost->get('email');
        $anrede      = $safePost->get('anrede');
        $titel       = $safePost->get('titel');
        $namenstitel = $safePost->get('namenstitel');
        $vorname     = $safePost->get('vorname');
        $name        = $safePost->get('name');
        $firma       = $safePost->get('firma');
        $strasse     = $safePost->get('strasse');
        $plz         = $safePost->get('plz');
        $ort         = $safePost->get('ort');
        $telefon     = $safePost->get('telefon');
        $telefax     = $safePost->get('telefax');
        $homepage    = $safePost->get('homepage');
		$mobil       = $safePost->get('mobil');

        $em = $this->iDao->getEm();
        $em->getConnection()->beginTransaction();
        try {

            $this->iDao->updateUserAccountEmail([
                'email'   => $email,
                'user_id' => $user_id
            ]);
            
            $this->iDao->updateUserInteressent([
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
                'mobil'       => $mobil,
                'user_id'     => $user_id
            ]);

            $em->getConnection()->commit();     
        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
    }

    public function interessentDelete($user_id){

        $em = $this->iDao->getEm();
        $em->getConnection()->beginTransaction();
        try {
		    $this->iDao->deleteInteressent([
			    'user_id' => $user_id
		    ]);

		    $em->getConnection()->commit();     
        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
    }

    public function InteressentList() {

        $stmt = $this->iDao->getAllInteressent();

        $rows = array();
        while ($row = $stmt->fetch()) {    
            $row2 = array();

            $row2[] = $row['userId'];
            $row2[] = $row['vorname'].' '.$row['name']; 
            $row2[] = $row['firma']; 
            $row2[] = $row['userEmail']; 
            // $row2[] = date("Y-m-d", (int)$row['registrierungsdatum']);    //=> string '1438597868' (length=10)
            // $row2[] = date("Y-m-d", (int)$row['lastlogin']);              // => string '1438954407' (length=10)
            $row2[] = $row['reg_date'];
            $row2[] = $row['last_login'];

            $str1 = "<a href=".$this->router->generate('interessent_edit', array('uid' => $row['userId'])).">Bearbeiten</a><br>";
            $str2 = "<a href=".$this->router->generate('interessent_pw_edit', array('uid' => $row['userId'])).">Passwort bearbeiten</a><br>";
            $str3 = "";
            if($row['gesperrt'] == 1){        
                $str3 = "<a href=".$this->router->generate('interessent_unlock', array('uid' => $row['userId'])).">Account entsperren</a><br>";
            } else {
                $str3 = "<a href=".$this->router->generate('interessent_lock', array('uid' => $row['userId'])).">Account sperren</a><br>";
            }
            $row2[] = $str1.$str2.$str3;
                          
            $rows[] = $row2;
        }

        return $rows;
    }

    public function InteressentDelList() {

        $stmt = $this->iDao->getDelInteressent();
    
        $rows = array();
        while ($row = $stmt->fetch()) {        
            $row2 = array();

            $row2[] = $row['userId']; 
            $row2[] = $row['vorname'].' '.$row['name'];
            $row2[] = $row['firma'];
            $row2[] = $row['userEmail'];
            // $row2[] = substr($row['loesch_datum'], 0, 10);
            $row2[] = $row['loesch_datum'];
            $str1 = "<a href=".$this->router->generate('interessent_delete', array('uid' => $row['userId'])).">Löschen</a><br>";
            $str2 = "<a href=".$this->router->generate('interessent_delete_undo', array('uid' => $row['userId'])).">Löschung zurücknehmen</a><br>";
            $row2[] = $str1.$str2;

            $rows[] = $row2;
        }

        return $rows;
    }
}