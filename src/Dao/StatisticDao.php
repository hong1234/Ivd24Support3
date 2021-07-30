<?php
namespace App\Dao;

class StatisticDao extends UserDao {

    public function getMaklerProRegion(iterable $values=[]){ // Die Anzahl der aktiven User pro Geschäftsstelle
        $sql = "SELECT 
                user_geschaeftsstelle.name, 
                user_geschaeftsstelle.geschaeftsstelle_id, 
                COUNT(user_makler.geschaeftsstelle_id) AS count_makler_on_regional_office
                FROM user_makler
                LEFT JOIN user_geschaeftsstelle ON user_makler.geschaeftsstelle_id = user_geschaeftsstelle.geschaeftsstelle_id
                GROUP BY user_makler.geschaeftsstelle_id";
        return $this->doQuery($sql, $values);
    }

    public function getMaklerOnRegion(iterable $values=[]){
        $sql = "SELECT
                user_geschaeftsstelle.name, 
                user_geschaeftsstelle.geschaeftsstelle_id, 
                COUNT(user_makler.geschaeftsstelle_id) AS count_makler_on_regional_office
                FROM user_makler
                LEFT JOIN user_geschaeftsstelle ON user_makler.geschaeftsstelle_id = user_geschaeftsstelle.geschaeftsstelle_id
                WHERE user_makler.geschaeftsstelle_id=:geschaeftsstelle_id";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getMaklerHaveObjectProRegion(iterable $values=[]){
        // Die Anzahl der aktiven User die auch Objekte bei ivd24 eingestellt haben
        // $query2 = "SELECT
        // user_geschaeftsstelle.name,
        // user_geschaeftsstelle.geschaeftsstelle_id,
        // count(user_geschaeftsstelle.geschaeftsstelle_id) AS count_makler_with_aktive_objectdata
        // FROM user_geschaeftsstelle
        // LEFT JOIN user_makler ON user_geschaeftsstelle.geschaeftsstelle_id = user_makler.geschaeftsstelle_id
        // RIGHT OUTER JOIN (SELECT * FROM objekt_master GROUP BY user_id) AS ob ON user_makler.user_id = ob.user_id
        // WHERE ob.user_id IS NOT NULL
        // GROUP BY user_geschaeftsstelle.geschaeftsstelle_id";

        $sql = "SELECT
                user_geschaeftsstelle.name,
                user_geschaeftsstelle.geschaeftsstelle_id,
                count(user_geschaeftsstelle.geschaeftsstelle_id) AS count_makler_with_aktive_objectdata
                FROM user_geschaeftsstelle
                LEFT JOIN user_makler ON user_geschaeftsstelle.geschaeftsstelle_id = user_makler.geschaeftsstelle_id
                LEFT JOIN (SELECT * FROM objekt_master GROUP BY user_id) AS ob ON user_makler.user_id = ob.user_id
                WHERE ob.user_id IS NOT NULL
                GROUP BY user_geschaeftsstelle.geschaeftsstelle_id";
        return $this->doQuery($sql, $values);
    }

    public function getMaklerHaveObjectOnRegion(iterable $values=[]){
        $sql = "SELECT
                user_geschaeftsstelle.name,
                user_geschaeftsstelle.geschaeftsstelle_id,
                COUNT(user_geschaeftsstelle.geschaeftsstelle_id) AS count_makler_with_aktive_objectdata
                FROM user_makler
                LEFT JOIN user_geschaeftsstelle ON user_geschaeftsstelle.geschaeftsstelle_id = user_makler.geschaeftsstelle_id
                LEFT JOIN (SELECT * FROM objekt_master GROUP BY user_id) AS ob ON user_makler.user_id = ob.user_id
                WHERE ob.user_id IS NOT NULL AND user_makler.geschaeftsstelle_id=:geschaeftsstelle_id";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getMaklerHaveObjectOnRegion12(iterable $values=[]){
        $sql = "SELECT
                user_geschaeftsstelle.name,
                user_geschaeftsstelle.geschaeftsstelle_id,
                COUNT(user_geschaeftsstelle.geschaeftsstelle_id) AS count_makler_with_aktive_objectdata
                FROM user_makler
                LEFT JOIN user_geschaeftsstelle ON user_geschaeftsstelle.geschaeftsstelle_id = user_makler.geschaeftsstelle_id
                LEFT JOIN (SELECT * FROM objekt_master GROUP BY user_id) AS ob ON user_makler.user_id = ob.user_id
                WHERE ob.user_id IS NOT NULL AND (user_makler.geschaeftsstelle_id=:geschaeftsstelle_id1 OR user_makler.geschaeftsstelle_id=:geschaeftsstelle_id2)";
        return $this->doQuery($sql, $values)->fetch();
    }

    //-------------

    public function getActivMakler(iterable $values=[]){
        $sql = "SELECT makler.user_id, makler.vorname, makler.name, makler.firma, makler.strasse, makler.plz, makler.ort, makler.email, makler.telefon
                FROM user_makler makler
                LEFT JOIN (SELECT * FROM objekt_master GROUP BY user_id) AS ob ON makler.user_id = ob.user_id
                WHERE ob.user_id IS NOT NULL AND makler.geschaeftsstelle_id=:geschaeftsstelle_id";
        return $this->doQuery($sql, $values)->fetchAll();
    }

    public function getInActivMakler(iterable $values=[]){
        $sql = "SELECT makler.user_id, makler.vorname, makler.name, makler.firma, makler.strasse, makler.plz, makler.ort, makler.email, makler.telefon
                FROM user_makler makler
                LEFT JOIN (SELECT * FROM objekt_master GROUP BY user_id) AS ob ON makler.user_id = ob.user_id
                WHERE ob.user_id IS NULL AND makler.geschaeftsstelle_id=:geschaeftsstelle_id";
        return $this->doQuery($sql, $values)->fetchAll();
    }

    //------------

    public function getExposeLast4WeekByUserId(iterable $values=[]){
        $sql = "SELECT count(*) AS expose_az FROM statistik_hits_objekte_v3 where objekt_user_id = :user_id AND datum > :timepoint";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getRequestLast4WeekByUserId(iterable $values=[]){
        $sql = "SELECT count(*) AS request_az FROM statistik_anfragen_objekte_v2 where objekt_user_id = :user_id AND datum > :timepoint";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getActivObjectAnzahlByUserId(iterable $values=[]) {
        $sql = "SELECT COUNT(objekt_id) AS objekt_az FROM objekt_master WHERE user_id = :user_id AND freigabe = 'J'";
        return $this->doQuery($sql, $values)->fetch();
    }

    //-------

    public function getRequestLast12Months(iterable $values=[]){
        $sql = "SELECT  DATE_FORMAT(an.datum, '%Y-%m') AS time_span, count(*) req_az 
                FROM statistik_anfragen_objekte_v2 an
                LEFT JOIN user_makler ON an.objekt_user_id = user_makler.user_id
                WHERE user_makler.user_id !=0
                GROUP BY time_span
                ORDER BY time_span ASC";
        return $this->doQuery($sql, $values)->fetchAll();
    }

    public function getRequestLast12MonthsByRegion(iterable $values=[]){
        $sql = "SELECT DATE_FORMAT(an.datum, '%Y-%m') AS time_span, count(*) req_az 
                FROM statistik_anfragen_objekte_v2 an
                LEFT JOIN user_makler ON an.objekt_user_id = user_makler.user_id
                WHERE user_makler.user_id !=0 AND user_makler.geschaeftsstelle_id = :geschaeftsstelle_id
                GROUP BY time_span
                ORDER BY time_span ASC";
        return $this->doQuery($sql, $values)->fetchAll();
    }

    public function getRequestLast12MonthsByRegion1And2(iterable $values=[]){
        $sql = "SELECT DATE_FORMAT(an.datum, '%Y-%m') AS time_span, count(*) req_az 
                FROM statistik_anfragen_objekte_v2 an
                LEFT JOIN user_makler ON an.objekt_user_id = user_makler.user_id
                WHERE user_makler.user_id !=0 AND (user_makler.geschaeftsstelle_id = 1 OR user_makler.geschaeftsstelle_id = 2)
                GROUP BY time_span
                ORDER BY time_span ASC";
        return $this->doQuery($sql, $values)->fetchAll();
    }

}