<?php
namespace App\Service;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Dao\UserDao;
use App\Entity\User;

class StatisticUserService
{
    private $uDao;
    private $passwordEncoder;

    function __construct(UserDao $uDao, UserPasswordEncoderInterface $passwordEncoder) {
        $this->uDao = $uDao;  
        $this->passwordEncoder = $passwordEncoder;
    }

    public function newStatisticUser($username, $email, $passwort, $gs_id) {
        $em = $this->uDao->getEm();
        $em->getConnection()->beginTransaction();
        try {
            $this->uDao->insertAccountForStatisticUser([
                'art_id'                =>  '4',
                'recht_id'              =>  '8',
                'geschaeftsstellen_id'  =>  $gs_id,         //Geschaeftsstellen_id 
                'kennwort'              =>  md5($passwort), // Kennwort = Siehe Beschreibung GUI
                'username'              =>  $username,      // Username = Siehe Beschreibung GUI
                'email'                 =>  $email,         // Email = Siehe Beschreibung GUI
                'regdate'               =>  time(),         // Registrierungsdatum = timestamp vom Zeitpunkt des Anlegens
                'authentifiziert'       =>  '1',            // Authentifiziert = 1
                'gesperrt'              =>  '0',            // gesperrt = 0
                'loeschung'             =>  '0',            // loeschung = 0
                'newsletter'            =>  'N'             // newsletter = 'N'
            ]);  

            $user_id = $em->getConnection()->lastInsertId();
            //-------------
    
            $user = new User();
            $user->setUserid($user_id);
            $user->setEmail($email);
            $user->setRoles(['ROLE_STATISTIC']);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $passwort
            ));
    
            $em->persist($user);
            $em->flush();
    
            //------------- 
            $em->getConnection()->commit();   

        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
                throw $e;
        }
    }

    public function updateStatisticUser($user_id, $username, $email, $passwort, $gs_id) {
        $em = $this->uDao->getEm();
        $em->getConnection()->beginTransaction();
        try {
            //-------------------------------
            $this->uDao->updateStatisticUser([
                'username'              => $username,
                'email'                 => $email,
                'kennwort'              => md5($passwort),
                'geschaeftsstellen_id'  => $gs_id,
                'user_id'               => $user_id
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

}