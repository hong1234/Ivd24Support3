<?php
namespace App\Validator;

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

    public function getCheckResult($rs) {

        $status = false;
        $user_id = 0;
        $person = '';
        
        if(count($rs)>0){
            $account = $rs[0];
            $status  = true;
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

    public function occupiedEmailName($email){
        $rs = $this->uDao->getUserAccountByEmail(['email' => $email]);
        return $this->getCheckResult($rs);
    }

    public function occupiedEmailNameByUpdate($pre_email, $email) {
        $rs = $this->uDao->getUserAccountByEmail2(['email' => $email, 'pre_email' => $pre_email]);
        return $this->getCheckResult($rs);
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

    public function getCheckResult2($rs) {
        
        $status = false;
        $user_id = 0;
        $person = '';
        
        if(count($rs)>0){
            $makler = $rs[0];
            $status = true;
            $user_id = $makler['user_id'];
            $person = 'Makler';
        }
        
        return ['status' => $status, 'user_id' => $user_id, 'person' => $person];
    }

    public function occupiedSeoUrl($seo_url){
        $rs = $this->mDao->getMaklerBySeoUrl(['seo_url' => $seo_url]);
        return $this->getCheckResult2($rs);
    }

    public function occupiedSeoUrlByUpdate($pre_seo_url, $seo_url){
        $rs = $this->mDao->getMaklerBySeoUrl2(['pre_seo_url' => $pre_seo_url, 'seo_url' => $seo_url]);
        return $this->getCheckResult2($rs);
    }

    //-----------Account----------------------------------------

    public function isValidAccountInput($username, $email, $passwort){

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

    public function isValidAccountInputByUpdate($user_id, $username, $email, $passwort){

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
            $pre_email = $user_makler['email'];
            $pre_seo_url = $user_makler['seo_url'];

            $rs = $this->occupiedEmailNameByUpdate($pre_email, $email);
            if($rs['status']){
                $user_id = $rs['user_id'];
                $person = $rs['person'];
                $error = $error."|--email schon belegt von $person (user_id: $user_id)--";
            }

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