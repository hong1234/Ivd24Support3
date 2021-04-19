<?php
namespace App\Dao;

class StatisticDao extends UserDao {

    public function getActivMaklerProRegion(iterable $values=[]){ // Die Anzahl der aktiven User pro Geschäftsstelle
        $sql    =   "SELECT 
                    user_geschaeftsstelle.name, 
                    user_geschaeftsstelle.geschaeftsstelle_id, 
                    COUNT(user_makler.geschaeftsstelle_id) AS count_makler_on_regional_office
                    FROM user_makler
                    LEFT JOIN user_geschaeftsstelle ON user_makler.geschaeftsstelle_id = user_geschaeftsstelle.geschaeftsstelle_id
                    GROUP BY user_makler.geschaeftsstelle_id";
        return $this->doQuery($sql, $values);
    }

    public function getActivMaklerOnRegion(iterable $values=[]){
        $sql = "SELECT
        user_geschaeftsstelle.name, 
        user_geschaeftsstelle.geschaeftsstelle_id, 
        COUNT(user_makler.geschaeftsstelle_id) AS count_makler_on_regional_office
        FROM user_makler
        LEFT JOIN user_geschaeftsstelle ON user_makler.geschaeftsstelle_id = user_geschaeftsstelle.geschaeftsstelle_id
        WHERE user_makler.geschaeftsstelle_id=:geschaeftsstelle_id";
        return $this->doQuery($sql, $values);
    }

    public function getActivMaklerHaveObjectProRegion(iterable $values=[]){
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

        $sql    =   "SELECT
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

    public function getActivMaklerHaveObjectOnRegion(iterable $values=[]){
        $sql = "SELECT
        user_geschaeftsstelle.name,
        user_geschaeftsstelle.geschaeftsstelle_id,
        COUNT(user_geschaeftsstelle.geschaeftsstelle_id) AS count_makler_with_aktive_objectdata
        FROM user_makler
        LEFT JOIN user_geschaeftsstelle ON user_geschaeftsstelle.geschaeftsstelle_id = user_makler.geschaeftsstelle_id
        LEFT JOIN (SELECT * FROM objekt_master GROUP BY user_id) AS ob ON user_makler.user_id = ob.user_id
        WHERE ob.user_id IS NOT NULL AND user_makler.geschaeftsstelle_id=:geschaeftsstelle_id";
        return $this->doQuery($sql, $values);
    }

}