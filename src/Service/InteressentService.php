<?php
namespace App\Service;  

use App\Dao\InteressentDao;

class InteressentService
{
    public $iDao;

    function __construct(InteressentDao $iDao) {
        $this->iDao = $iDao;
    }

    public function interessentUpdate($user_id, $safePost){

        $email        = $safePost->get('email');
        $anrede       = $safePost->get('anrede');
        $titel        = $safePost->get('titel');
        $namenstitel  = $safePost->get('namenstitel');
        $vorname      = $safePost->get('vorname');
        $name         = $safePost->get('name');
        $firma        = $safePost->get('firma');
        $strasse      = $safePost->get('strasse');
        $plz          = $safePost->get('plz');
        $ort          = $safePost->get('ort');
        $telefon      = $safePost->get('telefon');
        $telefax      = $safePost->get('telefax');
        $homepage     = $safePost->get('homepage');
		$mobil        = $safePost->get('mobil');

        $em = $this->iDao->getEm();
        $em->getConnection()->beginTransaction();
        try {

            $this->iDao->updateUserAccountEmail([
                'email'     => $email,
                'user_id'   => $user_id
            ]);
            
            $this->iDao->updateUserInteressent([
                'anrede'        => $anrede, 
                'titel'         => $titel, 
                'namenstitel'   => $namenstitel, 
                'name'          => $name, 
                'vorname'       => $vorname, 
                'firma'         => $firma, 
                'strasse'       => $strasse, 
                'plz'           => $plz, 
                'ort'           => $ort,  
                'email'         => $email, 
                'telefon'       => $telefon, 
                'telefax'       => $telefax, 
                'homepage'      => $homepage, 
                'mobil'         => $mobil,
                'user_id'       => $user_id
            ]);

            //--------
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
}