<?php
namespace App\Dao;

class ObjectDao extends BaseDao {
    public function getObjectTotal(iterable $values=[]) {
        $sql    =   "SELECT COUNT(objekt_id) AS Anzah_Gesamtl_Objekte FROM objekt_master";
        return $this->doQuery($sql, $values)->fetchAssociative();
    }
    public function getObjectActiv(iterable $values=[]) {
        $sql    =   "SELECT COUNT(objekt_id) AS Anzahl_freigegeben_Objekte FROM objekt_master WHERE freigabe = 'J'";
        return $this->doQuery($sql, $values)->fetchAssociative();
    }
    public function getObjectInActiv(iterable $values=[]) {
        $sql    =   "SELECT COUNT(objekt_id) AS Anzahl_nicht_freigegeben_Objekte FROM objekt_master WHERE freigabe = 'N'";
        return $this->doQuery($sql, $values)->fetchAssociative();
    }
    
}