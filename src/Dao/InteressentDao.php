<?php
namespace App\Dao;

//class MaklerDao extends BaseDao {
class InteressentDao extends UserDao {

    public function getAllInteressent(iterable $values=[]) {
        $sql    =   "SELECT user_interessent.user_id AS userId, registrierungsdatum, lastlogin, recht_id, art_id, user_interessent.email AS userEmail, username, gesperrt, anrede, vorname, name, firma  
                    FROM user_interessent 
                    INNER JOIN user_account ON user_interessent.user_id = user_account.user_id 
                    WHERE user_account.art_id = 1";
        return $this->doQuery($sql, $values);
    }

    public function getDelInteressent(iterable $values=[]) {
        $sql    =   "SELECT user_interessent.user_id AS userId, loeschung, loesch_datum, loesch_send, recht_id, art_id, user_interessent.email AS userEmail, username, anrede, vorname, name, firma  
                    FROM user_interessent 
                    INNER JOIN user_account ON user_interessent.user_id = user_account.user_id 
                    WHERE user_account.loeschung = 1 AND user_account.art_id = 1 AND NOW() >= DATE_ADD(user_account.loesch_datum, INTERVAL 7 DAY)";
        return $this->doQuery($sql, $values);
    }
    
    public function getInteressent(iterable $values=[]) {
        $sql = "SELECT anrede, titel, namenstitel, name, vorname, firma, strasse, plz, ort, land, email, telefon, fax, mobil, homepage 
                FROM user_interessent 
                WHERE user_id = :user_id";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function updateUserInteressent(iterable $values=[]){
        $sql   =   "UPDATE user_interessent 
                    SET
                    anrede              = :anrede, 
                    titel               = :titel, 
                    namenstitel         = :namenstitel, 
                    name                = :name, 
                    vorname             = :vorname, 
                    firma               = :firma, 
                    strasse             = :strasse, 
                    plz                 = :plz, 
                    ort                 = :ort,  
                    email               = :email, 
                    telefon             = :telefon, 
                    fax                 = :telefax, 
                    homepage            = :homepage, 
                    mobil               = :mobil
                    WHERE       user_id = :user_id
                    ";
        return $this->doSQL($sql, $values);
    }

    public function deleteInteressent(iterable $values=[]){
        $tabs = array(
            "user_account",
            "user_account_merkliste",
            "user_account_merkliste_experten",
            "user_interessent",
            "user_suchauftraege_neu",
            "objekt_vergleich",
            "outboundcalls",
            "outboundcalls_user_makler",
            "user_suchauftraege_send"
        );
        
        $sql = "";
        foreach ($tabs as $tab) {
            $sql = "DELETE FROM ".$tab." WHERE user_id = :user_id";
            $this->doSQL($sql, $values);
        }

        return true;
    }
}