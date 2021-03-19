<?php
namespace App\Dao;

class UserDao extends BaseDao {

    public function getAllSupportUser(iterable $values=[]) {
        $sql = "SELECT user_id, username, email, gesperrt, loeschung, authentifiziert, registrierungsdatum, lastlogin 
                FROM user_account WHERE art_id = 4 and recht_id = 9";
        return $this->doQuery($sql, $values);
    }

    public function getSupportUser(iterable $values=[]) {
        $sql = "SELECT user_id, username, email, gesperrt, loeschung, authentifiziert, registrierungsdatum, lastlogin 
                FROM user_account WHERE art_id = 4 and recht_id = 9 AND user_id = :user_id";
        return $this->doQuery($sql, $values)->fetchAssociative();
    }

    public function updateSupportUser(iterable $values=[]){
        $sql   =   "UPDATE user_account 
                    SET
                    username             = :username, 
                    email                = :email,
                    kennwort             = :kennwort
                    WHERE       user_id  = :user_id";
        return $this->doSQL($sql, $values);
    }

    public function deleteSupporter(iterable $values=[]) {
        $sql    =   "DELETE FROM user_account WHERE user_id = :user_id";
        return $this->doSQL($sql, $values);
    }

    //----------

    public function getAllStatisticUser(iterable $values=[]) {
        $sql = "SELECT user_id, username, user_account.email, gesperrt, loeschung, authentifiziert, registrierungsdatum, lastlogin, user_geschaeftsstelle.name AS geschaeftsstelle
                FROM user_account
                LEFT JOIN user_geschaeftsstelle ON user_geschaeftsstelle.geschaeftsstelle_id = user_account.geschaeftsstellen_id 
                WHERE art_id = 4 and recht_id = 8";
        return $this->doQuery($sql, $values);
    }

    public function getStatisticUser(iterable $values=[]) {
        $sql = "SELECT user_id, username, user_account.email, gesperrt, loeschung, authentifiziert, registrierungsdatum, lastlogin, user_geschaeftsstelle.geschaeftsstelle_id AS geschaeftsstelleId, user_geschaeftsstelle.name AS gs_name
                FROM user_account
                LEFT JOIN user_geschaeftsstelle ON user_geschaeftsstelle.geschaeftsstelle_id = user_account.geschaeftsstellen_id 
                WHERE art_id = 4 and recht_id = 8 AND user_id = :user_id";
        return $this->doQuery($sql, $values)->fetchAssociative();
    }

    public function updateStatisticUser(iterable $values=[]){
        $sql   =   "UPDATE user_account 
                    SET
                    username             = :username, 
                    email                = :email,
                    kennwort             = :kennwort,
                    geschaeftsstellen_id = :geschaeftsstellen_id
                    WHERE       user_id  = :user_id";
        return $this->doSQL($sql, $values);
    }

    //----------------------------------
    
    public function insertAccountForStatisticUser(iterable $values=[]){
        $sql = "INSERT INTO user_account SET 
                    art_id                = :art_id, 
                    recht_id              = :recht_id,
                    geschaeftsstellen_id  = :geschaeftsstellen_id,
                    kennwort              = :kennwort,
                    username              = :username,
                    email                 = :email,
                    registrierungsdatum   = :regdate,
                    authentifiziert       = :authentifiziert, 
                    gesperrt              = :gesperrt,
                    loeschung             = :loeschung,
                    newsletter            = :newsletter
                ";

        return $this->doSQL($sql, $values);
    }

    public function insertAccountForSupporter(iterable $values=[]){
        $sql = "INSERT INTO user_account SET 
                    art_id                = :art_id, 
                    recht_id              = :recht_id,
                    kennwort              = :kennwort,
                    username              = :username,
                    email                 = :email,
                    registrierungsdatum   = :regdate,
                    authentifiziert       = :authentifiziert, 
                    gesperrt              = :gesperrt,
                    loeschung             = :loeschung,
                    newsletter            = :newsletter
                ";

        return $this->doSQL($sql, $values);
    }

    //--------

    public function getUserAccount(iterable $values=[]){
        $sql = "SELECT username FROM user_account WHERE user_id = :user_id";
        return $this->doQuery($sql, $values)->fetchAssociative();
    }

    public function updateUserAccountEmail(iterable $values=[]){
        $sql  =  "UPDATE user_account SET email = :email WHERE user_id= :user_id";
        return $this->doSQL($sql, $values);
    }

    public function updateUserAccountPW(iterable $values=[]){
        $sql  =  "UPDATE user_account SET kennwort  = :crypt_passwort WHERE user_id = :user_id";
        return $this->doSQL($sql, $values);
    }

    public function updateUserAccountGesperrt(iterable $values=[]){
        $sql = "UPDATE user_account SET gesperrt = :gesperrt WHERE user_id = :user_id";
        return $this->doSQL($sql, $values);
    }

    public function updateUserAccountLoeschung(iterable $values=[]){
        $sql = "UPDATE user_account SET loeschung=0, loesch_send=0 WHERE user_id = :user_id";
        return $this->doSQL($sql, $values);
    }

}