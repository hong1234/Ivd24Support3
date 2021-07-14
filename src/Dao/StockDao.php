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

}