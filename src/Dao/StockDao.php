<?php
namespace App\Dao;

class StockDao extends BaseDao {

    public function getStockNumber(iterable $values=[]) {
        $sql = "SELECT count(aktien_id) AS AnzahlAktienGesamt FROM aktien";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getStockNumberWithStakeholder(iterable $values=[]) {
        $sql = "SELECT count(aktien.user_id) AS AnzahlAktienMitZuweisungZuStakeholder 
                FROM aktien 
                LEFT JOIN user_account ON user_account.user_id = aktien.user_id 
                WHERE aktien.user_id IS NOT NULL AND user_account.user_id_stakeholder IS NOT NULL";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getStockNumberWithOutStakeholder(iterable $values=[]) {
        $sql = "SELECT count(aktien.user_id) AS AnzahlAktienOhneZuweisungZuStakeholder 
                FROM aktien 
                LEFT JOIN user_account ON user_account.user_id = aktien.user_id 
                WHERE aktien.user_id IS NOT NULL AND user_account.user_id_stakeholder IS NULL";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getStockNumberAG(iterable $values=[]) {
        $sql = "SELECT count(aktien_id) AS AnzahlAktienAG FROM aktien WHERE user_id IS NULL";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getStakeholderStruktur(iterable $values=[]){
        $sql = "SELECT  user_stakeholder.user_id AS user_id_stakeholder, user_stakeholder.stakeholderinfos AS stakeholder, count(aktien.aktien_id) AS aktien_az, (SELECT count(aktien.aktien_id) FROM aktien WHERE aktien.user_id = user_account.user_id_stakeholder) AS aktien_az_stakeholder 
                FROM aktien 
                LEFT JOIN user_account ON user_account.user_id = aktien.user_id 
                LEFT JOIN user_stakeholder ON user_stakeholder.user_id = user_account.user_id_stakeholder 
                WHERE aktien.user_id IS NOT NULL AND user_account.user_id_stakeholder IS NOT NULL 
                GROUP BY user_account.user_id_stakeholder";
        return $this->doQuery($sql, $values);
    }

    public function shareHolderList(iterable $values=[]){
        // $sql = "SELECT user_makler.user_id, user_makler.mitgliedsnummer, user_makler.vorname, user_makler.name, user_makler.email, user_makler.firma, count(aktien.aktien_id) AS aktien_az, user_account.user_id_stakeholder, user_stakeholder.stakeholderinfos AS stakeholder
        //         FROM user_makler
        //         LEFT JOIN aktien ON user_makler.user_id = aktien.user_id
        //         LEFT JOIN user_account ON user_account.user_id = aktien.user_id
        //         LEFT JOIN user_stakeholder ON user_stakeholder.user_id = user_account.user_id_stakeholder";

        $sql = "SELECT user_makler.user_id, user_makler.mitgliedsnummer, user_makler.vorname, user_makler.name, user_makler.firma, user_makler.email,
                (SELECT count(aktien.user_id) FROM aktien WHERE aktien.user_id = user_makler.user_id) AS aktien_az,
                user_account.user_id_stakeholder, user_stakeholder.stakeholderinfos AS stakeholder
                FROM user_makler
                LEFT JOIN user_account ON user_account.user_id = user_makler.user_id
                LEFT JOIN user_stakeholder ON user_stakeholder.user_id = user_account.user_id_stakeholder
                WHERE user_account.art_id = 2 AND user_account.recht_id = 3
                ";
        return $this->doQuery($sql, $values);
    }

    public function notShareHolderList(iterable $values=[]){
        $sql = "SELECT m.user_id, m.mitgliedsnummer, m.vorname, m.name, m.firma, m.email, m.telefon, m.ort, DATE_FORMAT(user_account.created_at, '%Y-%m-%d') AS reg_date, DATE_FORMAT(user_account.last_login_at, '%Y-%m-%d') AS last_login
                FROM user_makler m
                INNER JOIN user_account ON user_account.user_id = m.user_id
                LEFT JOIN (SELECT * FROM aktien WHERE aktien.user_id IS NOT NULL GROUP BY aktien.user_id) AS ak  ON ak.user_id = m.user_id
                WHERE m.user_id != 0 AND user_account.art_id = 2 AND user_account.recht_id = 3 AND ak.user_id IS NULL";
        return $this->doQuery($sql, $values);
    }

    //---------

    public function getStakeholders(iterable $values=[]){
        $sql = "SELECT * FROM user_account where art_id = 5";
        return $this->doQuery($sql, $values)->fetchAll();
    }

    public function getMembersOfStakeholder(iterable $values=[]){
        $sql = "SELECT * FROM user_account where user_id_stakeholder = :user_id_stakeholder";
        return $this->doQuery($sql, $values)->fetchAll();
    }

    public function getAktienAnzahlOfUser(iterable $values=[]){
        $sql = "SELECT count(aktien_id) AS aktien_az FROM aktien WHERE user_id = :user_id";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getAktienAnzahlOfStakeholder(iterable $values=[]){
        $sql = "SELECT count(aktien.aktien_id) AS aktien_az 
                FROM aktien 
                LEFT JOIN user_account ON user_account.user_id = aktien.user_id 
                WHERE user_account.user_id_stakeholder = :user_id_stakeholder";
        return $this->doQuery($sql, $values)->fetch();
    }

    // public function getStakeholderStruktur2(iterable $values=[]){
    //     $sql = "SELECT count(aktien.aktien_id) as asd, user_stakeholder.stakeholderinfos 
    //             FROM aktien 
    //             LEFT JOIN user_account ON user_account.user_id = aktien.user_id 
    //             LEFT JOIN user_stakeholder ON user_stakeholder.user_id = user_account.user_id_stakeholder 
    //             WHERE aktien.user_id IS NOT NULL AND user_account.user_id_stakeholder IS NOT NULL 
    //             GROUP BY user_account.user_id_stakeholder";
    //     return $this->doQuery($sql, $values)->fetchAll();
    // }

    //---------------

    // eine Liste aller noch nicht verifizierten Aktienkäufe
    public function getNotVerifiedAktien(iterable $values=[]){
        $sql = "SELECT aktien.user_id, user_makler.mitgliedsnummer,  user_makler.vorname, user_makler.name, user_makler.firma, user_makler.email, user_account.lastlogin 
                FROM aktien
                LEFT JOIN user_makler ON user_makler.user_id = aktien.user_id
                LEFT JOIN user_account ON user_account.user_id = user_makler.user_id
                WHERE aktien.purchase_date IS NOT NULL AND aktien.purchase_verified = 0 
                GROUP BY aktien.user_id";
        return $this->doQuery($sql, $values);
    }

    // noch nicht verifizierten Aktienkäufe von dem user
    public function getNotVerifiedUserAktien(iterable $values=[]){
        $sql = "SELECT aktien_id, aktienhash, creation_date, purchase_date
                FROM aktien
                WHERE aktien.purchase_date IS NOT NULL AND aktien.purchase_verified = 0 AND aktien.user_id = :user_id";
        return $this->doQuery($sql, $values)->fetchAll();
    }

    public function getVeryfiMakler(iterable $values=[]){
        $sql = "SELECT user_makler.user_id, user_makler.mitgliedsnummer, user_makler.firma, user_makler.vorname, user_makler.name, user_makler.strasse, user_makler.plz, user_makler.ort, user_makler.email, user_makler.telefon, 
                (SELECT count(aktien.user_id) FROM aktien WHERE aktien.user_id = user_makler.user_id) AS aktien_az
                FROM user_makler
                WHERE user_makler.user_id = :user_id";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getAktienDoc(iterable $values=[]){
        $sql = "SELECT * FROM aktien_documents WHERE user_id = :user_id";
        return $this->doQuery($sql, $values)->fetchAll();
    }

    public function updateVerified(iterable $values=[]){
        $sql  = "UPDATE aktien SET purchase_verified = 1 WHERE user_id = :user_id";
        return $this->doSQL($sql, $values);
    }

    public function getAktienAnzahlByUserId(iterable $values=[]){
        $sql = "SELECT count(*) AS ak_anzahl FROM aktien WHERE purchase_date IS NOT NULL AND user_id = :user_id";
        return $this->doQuery($sql, $values)->fetch();
    }

}