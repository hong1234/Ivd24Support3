<?php
namespace App\Dao;

class GeoDao extends BaseDao {

    public function getBundeslandByPLZ(iterable $values=[]) {
        $sql = "SELECT plz, bundesland FROM db001_portal.geodb_plz_ort WHERE plz = :plz group by plz, bundesland";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getGeschaeftsstelleByBundesland(iterable $values=[]) {
        $sql = "SELECT geo_bundesland.geschaeftsstelle_id, user_geschaeftsstelle.name 
                FROM db001_portal.geo_bundesland 
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