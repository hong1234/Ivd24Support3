<?php
namespace App\Service;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Dao\UserDao;
use App\Entity\User;
// use App\Service\SendQueue;

use Twig\Environment;

class SupporterService
{
    private $uDao;
    private $twig;
    private $passwordEncoder;

    function __construct(UserDao $uDao, Environment $twig, UserPasswordEncoderInterface $passwordEncoder) {
        $this->uDao = $uDao; 
        $this->twig = $twig;
        $this->passwordEncoder = $passwordEncoder; 
    }

    public function newSupporter($safePost) {

        $username  =  $safePost->get('username');
        $email     =  $safePost->get('email');
        $passwort  =  $safePost->get('passwort');

        //--------------------
        $tpl = $this->twig->render('supporter/email.html.twig', [
            'username'  => $username,
            'email'     => $email,
            'passwort'  => $passwort
        ]);

        $sendername      = 'Ivd24Admin';
        $absender_mail   = 'noreply@ivd24immobilien.de';
        $empfaenger_name = $username;
        $empfaenger_mail = $email;
        $betreff         = 'You are registered as Supporter !';
        $nachricht_html  = $tpl;
        $nachricht_plain = "You are registered as Supporter! with username=$username ; email=$email ; passwort=$passwort";
        $insertdate      = time();

        //-------------------

        $em = $this->uDao->getEm();
        $em->getConnection()->beginTransaction();
        try {
            //--------------------        
            $this->uDao->insertAccountForSupporter([
                'art_id'            => '4',
                'recht_id'          => '9',
                'kennwort'          =>  md5($passwort), // Kennwort = Siehe Beschreibung GUI
                'username'          =>  $username,      // Username = Siehe Beschreibung GUI
                'email'             =>  $email,         // Email = Siehe Beschreibung GUI
                'regdate'           =>  time(),         // Registrierungsdatum = timestamp vom Zeitpunkt des Anlegens
                'authentifiziert'   => '1',             // Authentifiziert = 1
                'gesperrt'          => '0',             // gesperrt = 0
                'loeschung'         => '0',             // loeschung = 0
                'newsletter'        => 'N'              // newsletter = 'N'
        
            ]);
    
            $user_id  =  $em->getConnection()->lastInsertId();
            //-------------
            $user = new User();
            $user->setUserid($user_id);
            $user->setEmail($email);
            $user->setRoles(['ROLE_SUPPORT']);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $passwort
            ));
    
            $em->persist($user);
            $em->flush();
            //-------------
            
            $this->uDao->insertSendQueue([
                'sendername'        => $sendername, 
                'absender_mail'     => $absender_mail,
                'empfaenger_name'   => $empfaenger_name,
                'empfaenger_mail'   => $empfaenger_mail,
                'betreff'           => $betreff,
                'nachricht_html'    => $nachricht_html,
                'nachricht_plain'   => $nachricht_plain,
                'insertdate'        => $insertdate
            ]);
            //-------------
            
            $em->getConnection()->commit();   

        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
        
    }

    public function updateSupporter($user_id, $safePost) {
        $username = $safePost->get('username');
        $email    = $safePost->get('email');
        $passwort = $safePost->get('passwort');
            
        $em = $this->uDao->getEm();
        $em->getConnection()->beginTransaction();
        try {
            //------------------------
            $this->uDao->updateSupportUser([
                'username'    => $username,
                'email'       => $email,
                'kennwort'    => md5($passwort),
                'user_id'     => $user_id
            ]);
            //----------
            $user = $em->getRepository(User::class)->findOneBy(['userid' => $user_id]);
            $user->setEmail($email);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $passwort
            ));

            $em->flush();
            //---------- 

            $em->getConnection()->commit();   

        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
    }

    public function deleteSupporter($user_id) {

        $em = $this->uDao->getEm();
        $em->getConnection()->beginTransaction();
        try {
            //-------------------------
            $this->uDao->deleteSupporter([
                'user_id' => $user_id
            ]);

            $user = $em->getRepository(User::class)->findOneBy(['userid' => $user_id]);
            if ($user != null){
                $em->remove($user);
                $em->flush();
            }
            //---------- 

            $em->getConnection()->commit();   

        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
    }
}