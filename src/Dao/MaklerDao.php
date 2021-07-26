<?php
namespace App\Dao;

class MaklerDao extends UserDao {
    //----new Makler--------
    public function insertMakler(iterable $values=[]){
        $sql =  "INSERT INTO user_makler 
                SET
                user_id             = :user_id,
                geschaeftsstelle_id = :geschaeftsstelle_id, 
                mitgliedsnummer     = :mitgliedsnummer,
                mitgliedskategorien = :mkategorie_id, 
                m_konfig_id         = '1', 
                anrede              = :anrede, 
                titel               = :titel,
                namenstitel         = '', 
                name                = :name, 
                vorname             = :vorname, 
                firma               = :firma, 
                strasse             = :strasse, 
                plz                 = :plz, 
                ort                 = :ort, 
                geodb_laender_id    = '60', 
                email               = :email, 
                telefon             = :telefon, 
                fax                 = :telefax, 
                homepage            = :homepage, 
                seo_url             = :seo_url, 
                sortierung          = '1',
				ahu_geo_point	    = GeomFromText('POINT(1 1)'),  
                bundesland_id       = :bundesland_id
                ";
        return $this->doSQL($sql, $values);
    }

    public function insertUserMaklerConfig(iterable $values=[]){
        $sql = "INSERT INTO user_makler_config 
                SET 
                user_id            = :user_id, 
                anrede_anzeigen    = '1', 
                anrede_pflicht     = '0', 
                vorname_anzeigen   = '1', 
                vorname_pflicht    = '0', 
                nachname_anzeigen  = '1', 
                nachname_pflicht   = '1', 
                strasse_anzeigen   = '0', 
                strasse_pflicht    = '0',  
                plz_anzeigen       = '0',
                plz_pflicht        = '0', 
                ort_anzeigen       = '0', 
                ort_pflicht        = '0', 
                email_anzeigen     = '1', 
                email_pflicht      = '1', 
                telefon_anzeigen   = '1', 
                telefon_pflicht    = '0', 
                nachricht_anzeigen = '1', 
                nachricht_pflicht  = '0', 
                nachricht_text     = '', 
                widerruf_jn        = '0', 
                bilderserver_id    = :bilderserver_id, 
                bilderordner       = :bilderordner, 
                ftp_server_id      = :ftp_server_id, 
                ftp_benutzer       = :ftp_benutzer, 
                ftp_passwort       = :ftppasswort, 
                ftp_aktiv          = 'J', 
                ftp_pause          = 'N', 
                ftp_mod            = 'N', 
                ftp_del            = 'N', 
                move_robot_id      = :move_robot_id, 
                anzahl_objekte_pro_seite = '10',
                logo_pfad = '',
                returncode_businessclub = :returncode_businessclub
                ";
        return $this->doSQL($sql, $values);
    }

    public function insertRobotQueue(iterable $values=[]){
        $sql = "INSERT INTO robot_queue 
                SET 
                config_server_id = '5',
                robot_id         = '1',
                status           = 'QUEUED',
                robot_name       = 'FTPUserAdd',
                parameter1       = :homeftp,
                parameter2       = 'makler',
                parameter3       = :user_id,
                parameter4       = '/bin/false',
                parameter5       = '/etc/skel',
                parameter6       = :ftppasswortcrypt,
                parameter7       = :ftp_benutzer,
                parameter8       = '/usr/sbin/useradd'
                ";
        return $this->doSQL($sql, $values);
    }

    public function insertRobotQueue2(iterable $values=[]){
        $sql = "INSERT INTO robot_queue 
                SET 
                config_server_id = '5',
                robot_id         = '2',
                status           = 'QUEUED',
                robot_name       = 'FTPUserMod',        
                parameter6       = :crypt_ftppasswort,
                parameter7       = :ftp_benutzer,
                parameter8       = '/usr/sbin/usermod'
                ";
        return $this->doSQL($sql, $values);
    }

    public function updateMaklerAhuGeoPoint(iterable $values=[]){
        $sql = "UPDATE user_makler 
                SET ahu_geo_point = ST_GeomFromText('Point(0 0)') 
                WHERE user_id = :user_id";
        return $this->doSQL($sql, $values);
    }

    //----------------------
    public function getMaklerUserByUserid(iterable $values) {
        $sql = "SELECT user_id, username, email, gesperrt, loeschung, authentifiziert, registrierungsdatum, lastlogin 
                FROM user_account WHERE art_id = 2 and recht_id = 3 and user_id = :user_id";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getMaklerBySeoUrl(iterable $values=[]){
        $sql = "SELECT * FROM user_makler WHERE seo_url = :seo_url";
        return $this->doQuery($sql, $values)->fetchAll();
    }

    public function getMaklerBySeoUrl2(iterable $values=[]){
        $sql = "SELECT * FROM user_makler WHERE seo_url = :seo_url AND seo_url !=:pre_seo_url";
        return $this->doQuery($sql, $values)->fetchAll();
    }

    public function updateMakler(iterable $values=[]){
        $sql = "UPDATE user_makler 
                SET
                anrede   = :anrede, 
                titel    = :titel,
                name     = :name, 
                vorname  = :vorname, 
                firma    = :firma, 
                strasse  = :strasse, 
                plz      = :plz, 
                ort      = :ort,  
                email    = :email, 
                telefon  = :telefon, 
                fax      = :telefax, 
                homepage = :homepage, 
                seo_url  = :seo_url,
                mobil    = :mobil
                WHERE user_id = :user_id
                ";
        return $this->doSQL($sql, $values);
    }

    public function getAllMakler(iterable $values=[]) {
        $sql = "SELECT m.user_id AS userId, m.mitgliedsnummer, DATE_FORMAT(FROM_UNIXTIME(registrierungsdatum), '%Y-%m-%d') AS reg_date, DATE_FORMAT(FROM_UNIXTIME(lastlogin), '%Y-%m-%d') AS last_login, recht_id, art_id, m.email AS maklerEmail, username, gesperrt, loeschung, anrede, vorname, name, firma, seo_url, m.ort  
                FROM user_makler AS m 
                INNER JOIN user_account ON m.user_id = user_account.user_id 
                WHERE user_account.art_id = 2";
        return $this->doQuery($sql, $values);
    }

    public function getDelMakler(iterable $values=[]) {
        // $sql    =   "SELECT user_makler.user_id, vorname, name, firma, user_makler.email, mitgliedsnummer, loesch_datum
        //             FROM user_makler
        //             LEFT JOIN user_account ON user_makler.user_id = user_account.user_id
        //             WHERE loeschung = 1 AND art_id = 2 AND NOW() >= DATE_ADD(user_account.loesch_datum, INTERVAL 7 DAY)";
        
        // $sql =  "SELECT user_makler.user_id, vorname, name, firma, user_makler.email, mitgliedsnummer, loesch_datum
        //          FROM (SELECT * FROM user_account WHERE loeschung = 1 AND art_id = 2 AND NOW() >= DATE_ADD(user_account.loesch_datum, INTERVAL 7 DAY)) AS selected_user_account
        //          INNER JOIN user_makler ON user_makler.user_id = selected_user_account.user_id";//////////

        $sql = "SELECT user_makler.user_id, vorname, name, firma, user_makler.email, mitgliedsnummer, DATE_FORMAT(loesch_datum, '%Y-%m-%d') AS loesch_datum
                FROM (SELECT * FROM user_account WHERE loeschung = 1 AND art_id = 2) AS selected_user_account
                INNER JOIN user_makler ON user_makler.user_id = selected_user_account.user_id";

        return $this->doQuery($sql, $values);
    }

    public function updateUserMaklerForDelete(iterable $values=[]){
        $sql  = "UPDATE user_account 
                 SET 
                 loeschung = 1,
                 loesch_datum =  NOW()
                 WHERE user_id = :user_id";
        return $this->doSQL($sql, $values);
    }

    public function updateUserMaklerConfig(iterable $values=[]){
        $sql = "UPDATE user_makler_config 
                SET 
                ftp_passwort = :ftppasswort 
                WHERE user_id = :user_id";
        return $this->doSQL($sql, $values);
    }

    //--------------makler-delete related methode--------------------------------------------------------------

    public function getObjectsByUserId(iterable $values=[]) {
        $sql    =   "SELECT objekt_id  FROM objekt_master where user_id = :user_id";
        return $this->doQuery($sql, $values);
    }

    public function insertUserDelete(iterable $values=[]){
        $sql = "INSERT INTO user_delete
                SET 
                user_id    = :user_id, 
                status     = :status,
                bildpfad   = :bildpfad,
                bildserver = :bildserver,
                ftppfad    = :ftppfad,
                ftpserver  = :ftpserver
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

}