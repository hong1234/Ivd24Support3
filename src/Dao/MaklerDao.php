<?php
namespace App\Dao;

class MaklerDao extends BaseDao {

    public function getMakler(iterable $values=[]){
        $sql = "SELECT anrede, titel, namenstitel, name, vorname, firma, strasse, plz, ort, email, telefon, fax, mobil, homepage, seo_url 
                FROM user_makler WHERE user_id = :user_id";
        return $this->doQuery($sql, $values)->fetchAssociative();
    }

    public function getMaklerUserByUserid(iterable $values) {
        $sql = "SELECT user_id, username, email, gesperrt, loeschung, authentifiziert, registrierungsdatum, lastlogin 
                FROM user_account WHERE art_id = 2 and recht_id = 3 and user_id = :user_id";
        return $this->doQuery($sql, $values)->fetchAssociative();
    }

    public function updateMakler(iterable $values=[]){
        $sql   =   "UPDATE user_makler 
                    SET
                    anrede              = :anrede, 
                    titel               = :titel, 
                    namenstitel         = :namenstitel, 
                    name                = :name, 
                    vorname             = :vorname, 
                    firma               = :firma, 
                    strasse             = :strasse, 
                    plz                 = :plz, 
                    ort                 = :ort,  
                    email               = :email, 
                    telefon             = :telefon, 
                    fax                 = :telefax, 
                    homepage            = :homepage, 
                    seo_url             = :seo_url,
                    mobil               = :mobil
                    WHERE       user_id = :user_id
                    ";
        return $this->doSQL($sql, $values);
    }

    public function getAllMakler(iterable $values=[]) {
        $sql    =   "SELECT m.user_id AS userId, registrierungsdatum, lastlogin, recht_id, art_id, m.email AS maklerEmail, username, gesperrt, anrede, vorname, name, firma  
                    FROM user_makler AS m 
                    INNER JOIN user_account ON m.user_id = user_account.user_id 
                    WHERE user_account.art_id = 2";

        // $sql2   =   "SELECT m.user_id AS userId, registrierungsdatum, lastlogin, recht_id, art_id, m.email AS maklerEmail, username, gesperrt, anrede, vorname, name, firma  
        //             FROM user_makler m 
        //             LEFT JOIN user_account ON m.user_id = user_account.user_id 
        //             WHERE user_account.art_id = 2";
        return $this->doQuery($sql, $values);
    }

    public function getDelMakler(iterable $values=[]) {
        // $sql    =   "SELECT user_makler.user_id, vorname, name, firma, user_makler.email, mitgliedsnummer, loesch_datum
        //             FROM user_makler
        //             LEFT JOIN user_account ON user_makler.user_id = user_account.user_id
        //             WHERE loeschung = 1 AND art_id = 2 AND NOW() >= DATE_ADD(user_account.loesch_datum, INTERVAL 7 DAY)";
        
        $sql =  "SELECT user_makler.user_id, vorname, name, firma, user_makler.email, mitgliedsnummer, loesch_datum
                 FROM (SELECT * FROM user_account WHERE loeschung = 1 AND art_id = 2 AND NOW() >= DATE_ADD(user_account.loesch_datum, INTERVAL 7 DAY)) AS selected_user_account
                 INNER JOIN user_makler ON user_makler.user_id = selected_user_account.user_id";
        return $this->doQuery($sql, $values);
    }

    public function updateUserMaklerConfig(iterable $values=[]){
        $sql  = "UPDATE user_makler_config SET 
                    ftp_passwort  = :ftppasswort WHERE user_id = :user_id";
        return $this->doSQL($sql, $values);
    }

    public function insertRobotQueue2(iterable $values=[]){
        $sql = "INSERT INTO robot_queue SET 
                config_server_id  = '5',
                robot_id          = '2',
                status            = 'QUEUED',
                robot_name        = 'FTPUserMod',        
                parameter6        = :crypt_ftppasswort,
                parameter7        = :ftp_benutzer,
                parameter8        = '/usr/sbin/usermod'";

        return $this->doSQL($sql, $values);
    }

    //--------------makler-delete related methode--------------------------------------------------------------
    public function getMaklerConfig(iterable $values=[]) {
        $sql = "SELECT bilderserver_id, bilderordner, ftp_server_id, ftp_benutzer 
                FROM user_makler_config 
                WHERE user_id = :user_id";
        return $this->doQuery($sql, $values)->fetchAssociative();
    }

    public function getObjectsByUserId(iterable $values=[]) {
        $sql    =   "SELECT objekt_id  FROM objekt_master where user_id = :user_id";
        return $this->doQuery($sql, $values);
    }

    public function insertUserDelete(iterable $values=[]){
        $sql = "INSERT INTO user_delete
                SET 
                    user_id     = :user_id, 
                    status      = :status,
                    bildpfad    = :bildpfad,
                    bildserver  = :bildserver,
                    ftppfad     = :ftppfad,
                    ftpserver   = :ftpserver
                ";
        
        return $this->doSQL($sql, $values);  
    }

    public function deleteAttachmentsByObjectId(iterable $values=[]) {
        $sql    =   "DELETE FROM objekt_anhaenge WHERE objekt_id = :objekt_id";
        return $this->doSQL($sql, $values);
    }

    public function deleteAllByUserId(iterable $values=[]){
        $tabs = array(
                    "objekt_master", 
                    "user_account", 
                    "user_account_merkliste",
                    "user_account_merkliste_experten",
                    "user_interessent", 
                    "user_makler",
                    "user_makler_config",
                    "user_makler_ansprechpartner",
                    "user_makler_merkmale_bind",
                    "user_group_members",
                    "user_group_objects",
                    "user_suchauftraege_neu",
                    "user_suchauftraege_send",
                    "objektreferenzen",
                    "objekt_archiv",
                    "objekt_buero_praxen",
                    "objekt_einzelhandel",
                    "objekt_freizeitimmobilien_gewerblich",
                    "objekt_gastgewerbe",
                    "objekt_grundstueck",
                    "objekt_halle_lager_prod",
                    "objekt_haus",
                    "objekt_land_fortswirtschaft",
                    "objekt_parken_stellplatz",
                    "objekt_sonstige",
                    "objekt_vergleich",
                    "objekt_widerruf",
                    "objekt_wohnung",
                    "objekt_zimmer",
                    "objekt_zinshaus_renditeobjekt",
                    "outboundcalls",
                    "outboundcalls_user_makler",
                    "send_queue"
                );

        $sql = "";
        foreach ($tabs as $tab) {
            $sql = "DELETE FROM ".$tab." WHERE user_id = :user_id";
            $this->doSQL($sql, $values);
        }

        $sql = "DELETE FROM robot_queue WHERE parameter3 = :user_id";
        $this->doSQL($sql, $values);

        return true;
    }

    public function getActivMaklerProRegion (iterable $values=[]){ // Die Anzahl der aktiven User pro Geschäftsstelle
        $sql    =   "SELECT 
                    user_geschaeftsstelle.name, 
                    user_geschaeftsstelle.geschaeftsstelle_id, 
                    COUNT(user_makler.geschaeftsstelle_id) AS count_makler_on_regional_office
                    FROM user_makler
                    LEFT JOIN user_geschaeftsstelle ON user_makler.geschaeftsstelle_id = user_geschaeftsstelle.geschaeftsstelle_id
                    GROUP BY user_makler.geschaeftsstelle_id";
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

}