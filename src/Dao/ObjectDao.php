<?php
namespace App\Dao;

class ObjectDao extends BaseDao {

    public function getObjectTotal(iterable $values=[]) {
        $sql = "SELECT COUNT(objekt_id) AS Anzah_Gesamtl_Objekte FROM objekt_master";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getObjectActiv(iterable $values=[]) {
        $sql = "SELECT COUNT(objekt_id) AS Anzahl_freigegeben_Objekte FROM objekt_master WHERE freigabe = 'J'";
        return $this->doQuery($sql, $values)->fetch();
    }
    
    public function getObjectInActiv(iterable $values=[]) {
        $sql = "SELECT COUNT(objekt_id) AS Anzahl_nicht_freigegeben_Objekte FROM objekt_master WHERE freigabe = 'N'";
        return $this->doQuery($sql, $values)->fetch();
    }

    //-----------
    public function getObjectTotalByRegion(iterable $values=[]) {
        $sql = "SELECT COUNT(objekt_id) AS Anzah_Gesamtl_Objekte 
                FROM objekt_master
                LEFT JOIN user_makler ON user_makler.user_id = objekt_master.user_id
                WHERE user_makler.geschaeftsstelle_id = :geschaeftsstelle_id";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getObjectActivByRegion(iterable $values=[]) {
        $sql = "SELECT COUNT(objekt_id) AS Anzahl_freigegeben_Objekte 
                FROM objekt_master
                LEFT JOIN user_makler ON user_makler.user_id = objekt_master.user_id 
                WHERE objekt_master.freigabe = 'J' AND user_makler.geschaeftsstelle_id = :geschaeftsstelle_id";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getObjectInActivByRegion(iterable $values=[]) {
        $sql = "SELECT COUNT(objekt_id) AS Anzahl_nicht_freigegeben_Objekte 
                FROM objekt_master
                LEFT JOIN user_makler ON user_makler.user_id = objekt_master.user_id 
                WHERE freigabe = 'N' AND user_makler.geschaeftsstelle_id = :geschaeftsstelle_id";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getObjectTotalByRegion1And2(iterable $values=[]) {
        $sql = "SELECT COUNT(objekt_id) AS Anzah_Gesamtl_Objekte 
                FROM objekt_master
                LEFT JOIN user_makler ON user_makler.user_id = objekt_master.user_id
                WHERE user_makler.geschaeftsstelle_id = 1 OR user_makler.geschaeftsstelle_id = 2";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getObjectActivByRegion1And2(iterable $values=[]) {
        $sql = "SELECT COUNT(objekt_id) AS Anzahl_freigegeben_Objekte 
                FROM objekt_master
                LEFT JOIN user_makler ON user_makler.user_id = objekt_master.user_id 
                WHERE objekt_master.freigabe = 'J' AND (user_makler.geschaeftsstelle_id = 1 OR user_makler.geschaeftsstelle_id = 2)";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getObjectInActivByRegion1And2(iterable $values=[]) {
        $sql = "SELECT COUNT(objekt_id) AS Anzahl_nicht_freigegeben_Objekte 
                FROM objekt_master
                LEFT JOIN user_makler ON user_makler.user_id = objekt_master.user_id 
                WHERE freigabe = 'N' AND (user_makler.geschaeftsstelle_id = 1 OR user_makler.geschaeftsstelle_id = 2)";
        return $this->doQuery($sql, $values)->fetch();
    }

    //-----------

    public function insertObjectDaylyStatistic(iterable $values=[]) {
        $sql = "INSERT INTO hong_object_statistic 
                SET 
                object_gesamt     = :object_gesamt,
                object_frei       = :object_frei,
                object_nicht_frei = :object_nicht_frei,
                insertdate        = CURDATE()
                ";
        return $this->doSQL($sql, $values);
    }

    
    
}