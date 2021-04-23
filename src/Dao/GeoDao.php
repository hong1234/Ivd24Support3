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
        $state = $this->getBundeslandByPLZ([
            'plz' => $plz
        ]);
        $bundesland = $state['bundesland'];

        // $gs = $this->getGeschaeftsstelleByBundesland([
        //     'bundesland' => $bundesland
        // ]);

        $geo_bundesland  = $this->getRowInTableByIdentifier('geo_bundesland', ['bundesland' => $bundesland]);
        return $geo_bundesland;
    }
}