<?php
namespace App\Dao;

class ObjectDao extends BaseDao {

    public function getObjectTotal(iterable $values=[]) {
        $sql    =   "SELECT COUNT(objekt_id) AS Anzah_Gesamtl_Objekte FROM objekt_master";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getObjectActiv(iterable $values=[]) {
        $sql    =   "SELECT COUNT(objekt_id) AS Anzahl_freigegeben_Objekte FROM objekt_master WHERE freigabe = 'J'";
        return $this->doQuery($sql, $values)->fetch();
    }
    
    public function getObjectInActiv(iterable $values=[]) {
        $sql    =   "SELECT COUNT(objekt_id) AS Anzahl_nicht_freigegeben_Objekte FROM objekt_master WHERE freigabe = 'N'";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function insertObjectDaylyStatistic(iterable $values=[]) {
        $sql = "INSERT INTO hong_object_statistic SET 
                    object_gesamt      = :object_gesamt,
                    object_frei        = :object_frei,
                    object_nicht_frei  = :object_nicht_frei,
                    insertdate         = CURDATE()
                ";
        return $this->doSQL($sql, $values);
    }
    
}