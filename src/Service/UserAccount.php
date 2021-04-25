<?php
namespace App\Service;

use App\Dao\UserDao;
use App\Dao\MaklerDao;

class UserAccount
{
    private $uDao;
    private $mDao;

    function __construct(UserDao $uDao, MaklerDao $mDao) {
        $this->uDao = $uDao;
        $this->mDao = $mDao;
    }

    public function occupiedEmailName($email){

        $status = false;
        $person = '';
        $user_id = 0;

        $rs = $this->uDao->getUserAccountByEmail(['email' => $email]);

        if(count($rs)>0){
            $status = true;

            $account = $rs[0];
            $user_id = $account['user_id'];

            $user_art = $account['art_id'];
            if($user_art==1){
                $person = 'Interessent';
            }
            if($user_art==2){
                $person = 'Makler';
            }
            if($user_art==3){
                $person = 'Eigentuemer';
            }
            if($user_art==4){
                $person = 'Supporter';
            }
        }

        return ['status' => $status, 'user_id' => $user_id, 'person' => $person];
    }

    public function occupiedEmailNameByUpdate($pre_email, $email) {

        $status = false;
        $person = '';
        $user_id = 0;

        $rs = $this->uDao->getUserAccountByEmail2(['email' => $email, 'pre_email' => $pre_email]);

        if(count($rs)>0){
            $status = true;

            $account = $rs[0];
            $user_id  = $account['user_id'];

            $user_art = $account['art_id'];
            if($user_art==1){
                $person = 'Interessent';
            }
            if($user_art==2){
                $person = 'Makler';
            }
            if($user_art==3){
                $person = 'Eigentuemer';
            }
            if($user_art==4){
                $person = 'Supporter';
            }
        }
        
        return ['status' => $status, 'person' => $person, 'user_id' => $user_id];
    }

    // public function occupiedUserName($username){
    //     $status = false;
    //     if(count($this->uDao->getUserAccountByUserName(['username' => $username]))>0){
    //         $status = true;
    //     }
    //     return $status; 
    // }

    // public function occupiedUserNameByUpdate($pre_username, $username){
    //     $status = false;
    //     if(count($this->uDao->getUserAccountByUserName2(['pre_username' => $pre_username, 'username' => $username]))>0){
    //         $status = true;
    //     }
    //     return $status; 
    // }

    public function occupiedSeoUrl($seo_url){

        $status = false;
        $person = '';
        $user_id = 0;

        $rs = $this->mDao->getMaklerBySeoUrl(['seo_url' => $seo_url]);

        if(count($rs)>0){
            $status = true;

            $makler = $rs[0];
            $user_id = $makler['user_id'];
            $person = 'Makler';
        }
        
        return ['status' => $status, 'user_id' => $user_id, 'person' => $person];
    }

    public function occupiedSeoUrlByUpdate($pre_seo_url, $seo_url){
        $status = false;
        $person = '';
        $user_id = 0;

        $rs = $this->mDao->getMaklerBySeoUrl2(['pre_seo_url' => $pre_seo_url, 'seo_url' => $seo_url]);

        if(count($rs)>0){
            $status = true;

            $makler = $rs[0];
            $user_id = $makler['user_id'];
            $person = 'Makler';
        }
        //return $status;
        return ['status' => $status, 'user_id' => $user_id, 'person' => $person];
    }

    //-----------Account----------------------------------------

    public function isValidAccountName($username, $email, $passwort){

        $error = '';

        if($username == ''){
            $error = $error."|--username leer--";
        }
        if($email == ''){
            $error = $error."|--email leer--";
        }
        if($passwort == ''){
            $error = $error."|--passwort leer--";
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $rs = $this->occupiedEmailName($email);
            if($rs['status']){
                $person = $rs['person'];
                $user_id = $rs['user_id'];
                $error = $error."|--email schon belegt von $person (user_id: $user_id)--";
            } 

            // if($this->occupiedUserName($username)){
            //     $error = $error."|--username schon belegt--";
            // } 
            
        } else {
            $error = $error."|--invalid email format--";
        }

        return $error;
    }

    public function isValidAccountNameByUpdate($user_id, $username, $email, $passwort){

        $error = '';

        if($username == ''){
            $error = $error."|--username leer--";
        }
        if($email == ''){
            $error = $error."|--email leer--";
        }
        if($passwort == ''){
            $error = $error."|--passwort leer--";
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $user_account = $this->uDao->getRowInTableByIdentifier('user_account', [
                'user_id' => $user_id
            ]);
            
            $pre_email = $user_account['email'];
            $rs = $this->occupiedEmailNameByUpdate($pre_email, $email);
            if($rs['status']){
                $person = $rs['person'];
                $user_id = $rs['user_id'];
                $error = $error."|--email schon belegt von $person (user_id: $user_id)--";
            }

            // $pre_username = $user_account['username'];
            // if($this->occupiedUserNameByUpdate($pre_username, $username)){
            //     $error = $error."|--username schon belegt--";
            // } 
            
        } else {
            $error = $error."|--invalid email format--";
        }

        return $error;
    }

    //-----------Makler------------------------------------------------
    public function isValidMaklerData($username, $email, $passwort, $seo_url){

        $error = '';

        if($username == ''){
            $error = $error."|--username leer--";
        }
        if($email == ''){
            $error = $error."|--email leer--";
        }
        if($passwort == ''){
            $error = $error."|--passwort leer--";
        }
        if($seo_url == ''){
            $error = $error."|--seo_url leer--";
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $rs = $this->occupiedEmailName($email);
            if($rs['status']){
                $person = $rs['person'];
                $user_id = $rs['user_id'];
                $error = $error."|--email schon belegt von $person (user_id: $user_id)--";
            } 

            // if($this->occupiedUserName($username)){
            //     $error = $error."--username schon belegt--";
            // } 

            $rs = $this->occupiedSeoUrl($seo_url);
            if($rs['status']){
                $person = $rs['person'];
                $user_id = $rs['user_id'];
                $error = $error."|--seo_url schon belegt von $person (user_id: $user_id)--";
            } 
            
        } else {
            $error = $error."|--invalid email format--";
        }

        return $error;
    }

    public function isValidMaklerDataByUpdate($user_id, $email, $seo_url){
        $error = '';

        if($email == ''){
            $error = $error."|--email leer--";
        }

        if($seo_url == ''){
            $error = $error."|--seo_url leer--";
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $user_makler = $this->mDao->getRowInTableByIdentifier('user_makler', [
                'user_id' => $user_id
            ]);
            
            $pre_email   = $user_makler['email'];
            $rs = $this->occupiedEmailNameByUpdate($pre_email, $email);
            if($rs['status']){
                $user_id = $rs['user_id'];
                $person = $rs['person'];
                $error = $error."|--email schon belegt von $person (user_id: $user_id)--";
            }

            $pre_seo_url = $user_makler['seo_url'];
            $rs = $this->occupiedSeoUrlByUpdate($pre_seo_url, $seo_url);
            if($rs['status']){
                $user_id = $rs['user_id'];
                $person = $rs['person'];
                $error = $error."|--seo_url schon belegt von $person (user_id: $user_id)--";
            } 
            
        } else {
            $error = $error."|--invalid email format--";
        }

        return $error;
    }

}