<?php
namespace App\Dao;

class StockDao extends BaseDao {

    public function getStockNumber(iterable $values=[]) {
        $sql = "SELECT count(aktien_id) AS AnzahlAktienGesamt FROM aktien";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getStockNumberAG(iterable $values=[]) {
        $sql = "SELECT count(aktien_id) AS AnzahlAktienAG FROM aktien WHERE user_id IS NULL";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getStockNumberWithOutStakeholder(iterable $values=[]) {
        $sql = "SELECT count(aktien.user_id) AS AnzahlAktienOhneZuweisungZuStakeholder 
                FROM aktien 
                LEFT JOIN user_account ON user_account.user_id = aktien.user_id 
                WHERE aktien.user_id IS NOT NULL AND user_account.user_id_stakeholder IS NULL";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getStockNumberWithStakeholder(iterable $values=[]) {
        $sql = "SELECT count(aktien.user_id) AS AnzahlAktienMitZuweisungZuStakeholder 
        FROM aktien 
        LEFT JOIN user_account ON user_account.user_id = aktien.user_id 
        WHERE aktien.user_id IS NOT NULL AND user_account.user_id_stakeholder IS NOT NULL";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function shareholderList(iterable $values=[]){
        $sql = "";
        return $this->doQuery($sql, $values);
    }
}