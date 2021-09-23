<?php
namespace App\Dao;

class GeoDao extends BaseDao {

    public function getMaklerByDistanceKm(iterable $values=[]) {
        $sql = "SELECT
                user_id, firma, ort,
                FORMAT((6371 * acos(cos(radians(:latitude)) * cos(radians(ahu_latitude)) * cos(radians(ahu_longitude ) - radians(:longitude)) + sin(radians(:latitude)) * sin(radians(ahu_latitude)))), 2) AS distance
                FROM user_makler
                HAVING distance < 10
                ORDER BY distance
                LIMIT 0 , 100";
        return $this->doQueryObj($sql, $values)->fetchAll();
    }

    public function getBundeslandByPLZobj(iterable $values=[]) {
        $sql = "SELECT plz, bundesland FROM geodb_plz_ort WHERE plz = :plz GROUP BY plz, bundesland";
        return $this->doQueryObj($sql, $values)->fetch();
    }

    public function getBundeslandByPLZ(iterable $values=[]) {
        $sql = "SELECT plz, bundesland FROM geodb_plz_ort WHERE plz = :plz GROUP BY plz, bundesland";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getGeschaeftsstelleByBundesland(iterable $values=[]) {
        $sql = "SELECT geo_bundesland.geschaeftsstelle_id, user_geschaeftsstelle.name 
                FROM geo_bundesland 
                LEFT JOIN user_geschaeftsstelle ON geo_bundesland.geschaeftsstelle_id = user_geschaeftsstelle.geschaeftsstelle_id 
                WHERE bundesland = :bundesland";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getGeschaeftsstelleByPLZ(string $plz) {

        $state = $this->getBundeslandByPLZ(['plz' => $plz]);
        if ($state !== false){
            return $this->getRowInTableByIdentifier('geo_bundesland', ['bundesland' => $state['bundesland']]);
        }

        return false;
    }
}