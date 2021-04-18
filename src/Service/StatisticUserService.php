<?php
namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use App\Dao\UserDao;
use App\Dao\UserLoginDao;
use App\Service\SendQueue;

class StatisticUserService
{
    private $router;
    private $uDao;
    private $loginDao;
    private $sqSer;

    function __construct(UrlGeneratorInterface $router, UserDao $uDao, UserLoginDao $loginDao, SendQueue $sqSer) {
        $this->router   = $router;
        $this->uDao     = $uDao; 
        $this->loginDao = $loginDao; 
        $this->sqSer    = $sqSer;
    }

    public function geschaeftsstelleName($gs_id) {
        $gs = $this->uDao->getGeschaeftsstelle([
            'geschaeftsstelle_id' => $gs_id
        ]);
        return $gs['name'];
    }

    public function newStatisticUser($safePost) {

        $username  =  $safePost->get('username');
        $email     =  $safePost->get('email');
        $passwort  =  $safePost->get('passwort');
        $gs_id     =  $safePost->get('geschaeftsstelle');
        $geschaeftsstelle = $this->geschaeftsstelleName($gs_id);
        $roles = ['ROLE_STATISTIC'];

        $em = $this->uDao->getEm();
        $em->getConnection()->beginTransaction();
        try {
            $this->uDao->insertAccountForStatisticUser([
                'art_id'                =>  '4',
                'recht_id'              =>  '8',
                'geschaeftsstellen_id'  =>  $gs_id,         //Geschaeftsstellen_id 
                'kennwort'              =>  md5($passwort), 
                'username'              =>  $username,      
                'email'                 =>  $email,         
                'regdate'               =>  time(),         // Registrierungsdatum = timestamp vom Zeitpunkt des Anlegens
                'authentifiziert'       =>  '1',            // Authentifiziert = 1
                'gesperrt'              =>  '0',            // gesperrt = 0
                'loeschung'             =>  '0',            // loeschung = 0
                'newsletter'            =>  'N'             // newsletter = 'N'
            ]);  

            $user_id = $em->getConnection()->lastInsertId();
            
            $this->loginDao->addLoginUser($email, $passwort, $roles, $user_id);
    
            $this->sqSer->addToSendQueue('statisticuser_new', [
                'username' => $username, 
                'email' => $email, 
                'passwort' => $passwort,
                'geschaeftsstelle' => $geschaeftsstelle
            ]);
    
            //------------- 
            $em->getConnection()->commit();   

        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
                throw $e;
        }
    }

    public function updateStatisticUser($user_id, $safePost) {

        $username = $safePost->get('username');
        $email    = $safePost->get('email');
        $passwort = $safePost->get('passwort');
        $gs_id    = $safePost->get('geschaeftsstelle');
        $geschaeftsstelle = $this->geschaeftsstelleName($gs_id);
        $roles = ['ROLE_STATISTIC'];

        $em = $this->uDao->getEm();
        $em->getConnection()->beginTransaction();
        try {
            
            $this->uDao->updateStatisticUser([
                'username'              => $username,
                'email'                 => $email,
                'kennwort'              => md5($passwort),
                'geschaeftsstellen_id'  => $gs_id,
                'user_id'               => $user_id
            ]);
            
            $this->loginDao->updateLoginUser($email, $passwort, $roles, $user_id);

            $this->sqSer->addToSendQueue('statisticuser_edit', [
                'username' => $username, 
                'email' => $email, 
                'passwort' => $passwort,
                'geschaeftsstelle' => $geschaeftsstelle
            ]);
            
            //---------- 
            $em->getConnection()->commit();   

        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
    }

    public function deleteStatisticUser($user_id) {

        $em = $this->uDao->getEm();
        $em->getConnection()->beginTransaction();
        try {
            $this->uDao->deleteStatisticUser([
                'user_id' => $user_id
            ]);

            $this->loginDao->deleteLoginUser($user_id);
             
            //----------
            $em->getConnection()->commit();   

        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
    }

    public function StatisticUserList() {

        $stmt = $this->uDao->getAllStatisticUser();

        $rows = array();
        while ($row = $stmt->fetch()) {
            $row2 = array();
            $row2[] = $row['user_id'];
            $row2[] = $row['username'];
            $row2[] = $row['email'];
            $row2[] = $row['geschaeftsstelle'];
            $row2[] = date("Y-m-d", (int)$row['registrierungsdatum']);
            $row2[] = date("Y-m-d", (int)$row['lastlogin']);
            $row2[] = "<a href=".$this->router->generate('statisticuser_edit', array('uid' => $row['user_id'])).">Daten bearbeiten</a>";
            $row2[] = "<a href=".$this->router->generate('statisticuser_delete', array('uid' => $row['user_id'])).">Statistic-User löschen</a>";
            
            $rows[] = $row2;
        }
        return $rows;
    }

}