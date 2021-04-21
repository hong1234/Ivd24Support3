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

    public function isValidAccountName($username, $email, $passwort){

        $error = '';
        if($username == ''){
            $error = $error."--username leer--";
        }
        if($email == ''){
            $error = $error."--email leer--";
        }
        if($passwort == ''){
            $error = $error."--passwort leer--";
        }
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            if($this->occupiedEmailName($email)){
                $error = $error."--email schon belegt--";
            } 

            if($this->occupiedUserName($username)){
                $error = $error."--username schon belegt--";
            } 
            
        } else {
            $error = $error."--invalid email format--";
        }
        return $error;
    }

    public function occupiedEmailName($email){
        $status = false;
        if(count($this->uDao->getUserAccountByEmail(['email' => $email]))>0){
            $status = true;
        }
        return $status;
    }

    public function occupiedUserName($username){
        $status = false;
        if(count($this->uDao->getUserAccountByUserName(['username' => $username]))>0){
            $status = true;
        }
        return $status; 
    }

    //------------------------
    public function isValidAccountNameByUpdate($user_id, $username, $email, $passwort){

        $error = '';
        if($username == ''){
            $error = $error."--username leer--";
        }
        if($email == ''){
            $error = $error."--email leer--";
        }
        if($passwort == ''){
            $error = $error."--passwort leer--";
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $user_account = $this->uDao->getRowInTableByIdentifier('user_account', [
                'user_id' => $user_id
            ]);
            
            $pre_email = $user_account['email'];
            $pre_username = $user_account['username'];

            if($this->occupiedEmailNameByUpdate($pre_email, $email)){
                $error = $error."--email schon belegt--";
            } 

            if($this->occupiedUserNameByUpdate($pre_username, $username)){
                $error = $error."--username schon belegt--";
            } 
            
        } else {
            $error = $error."--invalid email format--";
        }
        return $error;
    }

    public function occupiedEmailNameByUpdate($pre_email, $email){
        $status = false;
        if(count($this->uDao->getUserAccountByEmail2(['pre_email' => $pre_email, 'email' => $email]))>0){
            $status = true;
        }
        return $status;
    }

    public function occupiedUserNameByUpdate($pre_username, $username){
        $status = false;
        if(count($this->uDao->getUserAccountByUserName2(['pre_username' => $pre_username, 'username' => $username]))>0){
            $status = true;
        }
        return $status; 
    }

    //------------------
    public function isValidMaklerData($username, $email, $passwort, $seo_url){

        $error = '';

        if($username == ''){
            $error = $error."--username leer--";
        }
        if($email == ''){
            $error = $error."--email leer--";
        }
        if($passwort == ''){
            $error = $error."--passwort leer--";
        }
        if($seo_url == ''){
            $error = $error."--seo_url leer--";
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            if($this->occupiedEmailName($email)){
                $error = $error."--email schon belegt--";
            } 

            if($this->occupiedUserName($username)){
                $error = $error."--username schon belegt--";
            } 

            if($this->occupiedSeoUrl($seo_url)){
                $error = $error."--seo_url schon belegt--";
            } 
            
        } else {
            $error = $error."--invalid email format--";
        }

        return $error;
    }

    public function occupiedSeoUrl($seo_url){
        $status = false;
        if(count($this->mDao->getMaklerBySeoUrl(['seo_url' => $seo_url]))>0){
            $status = true;
        }
        return $status;
    }

}