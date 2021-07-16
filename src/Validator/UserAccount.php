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

    public function occupiedEmailName($email){
        $rs = $this->uDao->getUserAccountByEmail(['email' => $email]);
        return $this->getCheckResult($rs);
    }

    public function occupiedEmailNameByUpdate($pre_email, $email) {
        $rs = $this->uDao->getUserAccountByEmail2(['email' => $email, 'pre_email' => $pre_email]);
        return $this->getCheckResult($rs);
    }

    public function occupiedUserName($username){
        $rs = $this->uDao->getUserAccountByUserName(['username' => $username]);
        return $this->getCheckResult($rs); 
    }

    public function occupiedUserNameByUpdate($pre_username, $username){
        $rs = $this->uDao->getUserAccountByUserName2(['pre_username' => $pre_username, 'username' => $username]);
        return $this->getCheckResult($rs); 
    }

    public function occupiedSeoUrl($seo_url){
        $rs = $this->mDao->getMaklerBySeoUrl(['seo_url' => $seo_url]);
        return $this->getCheckResult2($rs);
    }

    public function occupiedSeoUrlByUpdate($pre_seo_url, $seo_url){
        $rs = $this->mDao->getMaklerBySeoUrl2(['pre_seo_url' => $pre_seo_url, 'seo_url' => $seo_url]);
        return $this->getCheckResult2($rs);
    }

    //--------------
    public function isEmptyUsername($username){
        $error = '';
        if($username == ''){
            $error = "--username leer";
        }
        return $error;
    }
    public function isEmptyEmail($email){
        $error = '';
        if($email == ''){
            $error = "--email leer";
        }
        return $error;
    }
    public function isEmptyPasswort($passwort){
        $error = '';
        if($passwort == ''){
            $error = "--passwort leer";
        }
        return $error;
    }

    public function isEmptyFtpPasswort($ftppasswort){
        $error = '';
        if($ftppasswort == ''){
            $error = "--FtpPasswort leer";
        }
        return $error;
    }

    public function isEmptySeoUrl($seo_url){
        $error = '';
        if($seo_url == ''){
            $error = "--seo_url leer";
        }
        return $error;
    }

    public function isValidEmail($email){
        $error = '';
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $rs = $this->occupiedEmailName($email);
            if($rs['status']){
                $person = $rs['person'];
                $user_id = $rs['user_id'];
                $error = $error."--email schon belegt von $person (user_id: $user_id)";
            } 
            
        } else {
            $error = $error."--invalid email format";
        }

        return $error;
    }

    public function isValidEmailByUpdate($user_id, $email){
        $error = '';
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $user_account = $this->uDao->getRowInTableByIdentifier('user_account', [
                'user_id' => $user_id
            ]);
            $pre_email = $user_account['email'];
            
            $rs = $this->occupiedEmailNameByUpdate($pre_email, $email);
            if($rs['status']){
                $user_id = $rs['user_id'];
                $person = $rs['person'];
                $error = $error."--email schon belegt von $person (user_id: $user_id)";
            }
            
        } else {
            $error = $error."--invalid email format";
        }

        return $error; 
    }

    public function isValidSeoUrl($seo_url){
        $error = '';
        $rs = $this->occupiedSeoUrl($seo_url);
        if($rs['status']){
            $user_id = $rs['user_id'];
            $person = $rs['person'];
            $error = $error."--seo_url schon belegt von $person (user_id: $user_id)";
        } 
        return $error;
    }

    public function isValidSeoUrlByUpdate($user_id, $seo_url){
        $error = '';

        $user_makler = $this->mDao->getRowInTableByIdentifier('user_makler', [
            'user_id' => $user_id
        ]);
        $pre_seo_url = $user_makler['seo_url'];

        $rs = $this->occupiedSeoUrlByUpdate($pre_seo_url, $seo_url);
        if($rs['status']){
            $user_id = $rs['user_id'];
            $person = $rs['person'];
            $error = $error."--seo_url schon belegt von $person (user_id: $user_id)";
        }

        return $error;
    }

    public function isValidUserName($username){
        $error = '';
        $rs = $this->occupiedUserName($username);
        if($rs['status']){
            $person = $rs['person'];
            $user_id = $rs['user_id'];
            $error = $error."--username schon belegt von $person (user_id: $user_id)";
            } 
        return $error;
    }

    public function isValidUserNameByUpdate($user_id, $username){
        $error = '';

        $user_account = $this->uDao->getRowInTableByIdentifier('user_account', [
            'user_id' => $user_id
        ]);
        $pre_username = $user_account['username'];
        
        $rs = $this->occupiedUserNameByUpdate($pre_username, $username);
        if($rs['status']){
            $user_id = $rs['user_id'];
            $person = $rs['person'];
            $error = $error."--username schon belegt von $person (user_id: $user_id)";
        }

        return $error; 
    }

    //-------------

    public function isValidMaklerInput($safePost){
        
        $username = $safePost->get('username');
        $email    = $safePost->get('email'); 
        $passwort = $safePost->get('passwort');
        $ftppasswort = $safePost->get('ftppasswort');
        $seo_url  = $safePost->get('seo_url');

        $error = '';
        
        if($username == ''){
            $error = $error."--username leer";
        }
        if($email == ''){
            $error = $error."--email leer";
        }
        if($passwort == ''){
            $error = $error."--passwort leer";
        }
        if($ftppasswort == ''){
            $error = $error."--FtpPasswort leer";
        }
        if($seo_url == ''){
            $error = $error."--seo_url leer";
        }

        $error = $error.$this->isValidUserName($username);
        $error = $error.$this->isValidEmail($email);
        $error = $error.$this->isValidSeoUrl($seo_url);

        return $error;
    }

    public function isValidMaklerInputByUpdate($user_id, $safePost){
       
        $email = $safePost->get('email');
        $seo_url = $safePost->get('seo_url');

        $error = '';
        if($email == ''){
            $error = $error."--email leer";
        }
        if($seo_url == ''){
            $error = $error."--seo_url leer";
        }
        
        $error = $error.$this->isValidEmailByUpdate($user_id, $email);
        $error = $error.$this->isValidSeoUrlByUpdate($user_id, $seo_url);

        return $error;
    }

    //---------------
    public function isValidSupporterInput($safePost){

        $username = $safePost->get('username'); 
        $email    = $safePost->get('email'); 
        $passwort = $safePost->get('passwort');

        $error = '';

        if($username == ''){
            $error = $error."--username leer";
        }
        if($email == ''){
            $error = $error."--email leer";
        }
        if($passwort == ''){
            $error = $error."--passwort leer";
        }
            
        $error = $error.$this->isValidUserName($username);
        $error = $error.$this->isValidEmail($email);
        
        return $error;
    }

    public function isValidSupporterInputByUpdate($user_id, $safePost){

        $username = $safePost->get('username');
        $email    = $safePost->get('email');
        $passwort = $safePost->get('passwort');

        $error = '';  

        if($username == ''){
            $error = $error."--username leer";
        }
        if($email == ''){
            $error = $error."--email leer";
        }
        if($passwort == ''){
            $error = $error."--passwort leer";
        }
            
        $error = $error.$this->isValidUserNameByUpdate($user_id, $username);
        $error = $error.$this->isValidEmailByUpdate($user_id, $email);
        
        return $error;
    }

    //-----------------
    public function isValidStatisticUserInput($safePost){

        $username = $safePost->get('username');
        $email    = $safePost->get('email');
        $passwort = $safePost->get('passwort');
        $gs_id    = $safePost->get('geschaeftsstelle');

        //validation
        $error = '';

        $error = $error.$this->isEmptyUsername($username);
        $error = $error.$this->isEmptyEmail($email);
        $error = $error.$this->isEmptyPasswort($passwort);
            
        $error = $error.$this->isValidUserName($username);
        $error = $error.$this->isValidEmail($email);

        return $error;
    }

    public function isValidStatisticUserInputByUpdate($user_id, $safePost){

        $username = $safePost->get('username');
        $email    = $safePost->get('email');
        $passwort = $safePost->get('passwort');
        // $gs_id    = $safePost->get('geschaeftsstelle');

        $error = '';
        $error = $error.$this->isEmptyUsername($username);
        $error = $error.$this->isEmptyEmail($email);
        $error = $error.$this->isEmptyPasswort($passwort);

        $error = $this->isValidUserNameByUpdate($user_id, $username);
        $error = $this->isValidEmailByUpdate($user_id, $email);
        
        return $error;
    }

}