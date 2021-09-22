<?php
namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use App\Dao\UserDao;
use App\Dao\UserLoginDao;
use App\Service\SendQueue;

class SupporterService
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

    public function newSupporter($safePost) {

        $username = $safePost->get('username');
        $email    = $safePost->get('email');
        $passwort = $safePost->get('passwort');
        $roles    = ['ROLE_SUPPORT'];

        $conn = $this->uDao->dbConnection();
        $conn->beginTransaction();
        try {    
            $this->uDao->insertAccountForSupporter([
                'art_id'          => '4',
                'recht_id'        => '9',
                'geschaeftsstellen_id' => '6',
                'kennwort'        =>  md5($passwort), 
                'username'        =>  $username,      
                'email'           =>  $email,         
                'regdate'         =>  time(),         // Registrierungsdatum = timestamp vom Zeitpunkt des Anlegens
                'authentifiziert' => '1',             // Authentifiziert = 1
                'gesperrt'        => '0',             
                'loeschung'       => '0',             
                'newsletter'      => 'N'   
            ]);
    
            $user_id = $conn->lastInsertId();
            
            $this->loginDao->addLoginUser($email, $passwort, $roles, $user_id);
            
            $this->sqSer->addToSendQueue('supporter_new', [
                'user_id'   => $user_id,
                'email'     => $email,
                'username'  => $username, 
                'passwort'  => $passwort
            ]);

            $conn->commit();   
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
        
    }

    public function updateSupporter($user_id, $safePost) {

        $username = $safePost->get('username');
        $email    = $safePost->get('email');
        $passwort = $safePost->get('passwort');
        $roles = ['ROLE_SUPPORT'];
            
        $conn = $this->uDao->dbConnection();
        $conn->beginTransaction();
        try {
            $this->uDao->updateSupportUser([
                'username' => $username,
                'email'    => $email,
                'kennwort' => md5($passwort),
                'user_id'  => $user_id
            ]);
            
            $this->loginDao->updateLoginUser($email, $passwort, $roles, $user_id);
             
            $this->sqSer->addToSendQueue('supporter_edit', [
                'user_id'  => $user_id,
                'email'    => $email,
                'username' => $username,  
                'passwort' => $passwort
            ]);

            $conn->commit();   
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function deleteSupporter($user_id) {

        $conn = $this->uDao->dbConnection();
        $conn->beginTransaction();
        try {
            $this->uDao->deleteSupporter([
                'user_id' => $user_id
            ]);

            $this->loginDao->deleteLoginUser($user_id);
             
            $conn->commit();   
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function SupporterList() {

        $stmt = $this->uDao->getAllSupportUser();

        $rows = array();
        while ($row = $stmt->fetch()) {
            $row2 = array();
            $row2[] = $row['user_id'];
            $row2[] = $row['username'];
            $row2[] = $row['email'];
            $row2[] = date("Y-m-d", (int)$row['registrierungsdatum']);
            $row2[] = date("Y-m-d", (int)$row['lastlogin']);
            $row2[] = "<a href=".$this->router->generate('supporter_edit', array('uid' => $row['user_id'])).">Daten bearbeiten</a>";
            $row2[] = "<a href=".$this->router->generate('supporter_delete', array('uid' => $row['user_id'])).">Supporter löschen</a>";
            
            $rows[] = $row2;
        }

        return $rows;
    }
}