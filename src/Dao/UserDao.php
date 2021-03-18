<?php
namespace App\Dao;

class UserDao extends BaseDao {

    public function getAllSupportUser(iterable $values=[]) {
        $sql = "SELECT user_id, username, email, gesperrt, loeschung, authentifiziert, registrierungsdatum, lastlogin 
                FROM user_account WHERE art_id = 4 and recht_id = 9";
        return $this->doQuery($sql, $values);
    }

    public function getSupportUser(iterable $values=[]) {
        $sql = "SELECT user_id, username, email, gesperrt, loeschung, authentifiziert, registrierungsdatum, lastlogin 
                FROM user_account WHERE art_id = 4 and recht_id = 9 AND user_id = :user_id";
        return $this->doQuery($sql, $values)->fetchAssociative();
    }

    public function updateSupportUser(iterable $values=[]){
        $sql   =   "UPDATE user_account 
                    SET
                    username             = :username, 
                    email                = :email,
                    kennwort             = :kennwort
                    WHERE       user_id  = :user_id";
        return $this->doSQL($sql, $values);
    }

    public function deleteSupporter(iterable $values=[]) {
        $sql    =   "DELETE FROM user_account WHERE user_id = :user_id";
        return $this->doSQL($sql, $values);
    }

    //----------

    public function getAllStatisticUser(iterable $values=[]) {
        $sql = "SELECT user_id, username, user_account.email, gesperrt, loeschung, authentifiziert, registrierungsdatum, lastlogin, user_geschaeftsstelle.name AS geschaeftsstelle
                FROM user_account
                LEFT JOIN user_geschaeftsstelle ON user_geschaeftsstelle.geschaeftsstelle_id = user_account.geschaeftsstellen_id 
                WHERE art_id = 4 and recht_id = 8";
        return $this->doQuery($sql, $values);
    }

    public function getStatisticUser(iterable $values=[]) {
        $sql = "SELECT user_id, username, user_account.email, gesperrt, loeschung, authentifiziert, registrierungsdatum, lastlogin, user_geschaeftsstelle.geschaeftsstelle_id AS geschaeftsstelleId, user_geschaeftsstelle.name AS gs_name
                FROM user_account
                LEFT JOIN user_geschaeftsstelle ON user_geschaeftsstelle.geschaeftsstelle_id = user_account.geschaeftsstellen_id 
                WHERE art_id = 4 and recht_id = 8 AND user_id = :user_id";
        return $this->doQuery($sql, $values)->fetchAssociative();
    }

    public function updateStatisticUser(iterable $values=[]){
        $sql   =   "UPDATE user_account 
                    SET
                    username             = :username, 
                    email                = :email,
                    kennwort             = :kennwort,
                    geschaeftsstellen_id = :geschaeftsstellen_id
                    WHERE       user_id  = :user_id";
        return $this->doSQL($sql, $values);
    }

    //----------------------------------

    public function getInteressent(iterable $values=[]) {
        $sql = "SELECT anrede, titel, namenstitel, name, vorname, firma, strasse, plz, ort, land, email, telefon, fax, mobil, homepage 
                FROM user_interessent 
                WHERE user_id = :user_id";
        return $this->doQuery($sql, $values)->fetchAssociative();
    }

    public function getAllInteressent(iterable $values=[]) {
        $sql    =   "SELECT user_interessent.user_id AS userId, registrierungsdatum, lastlogin, recht_id, art_id, user_interessent.email AS userEmail, username, gesperrt, anrede, vorname, name, firma  
                    FROM user_interessent 
                    INNER JOIN user_account ON user_interessent.user_id = user_account.user_id 
                    WHERE user_account.art_id = 1";
        return $this->doQuery($sql, $values);
    }

    public function getDelInteressent(iterable $values=[]) {
        $sql    =   "SELECT user_interessent.user_id AS userId, loeschung, loesch_datum, loesch_send, recht_id, art_id, user_interessent.email AS userEmail, username, anrede, vorname, name, firma  
                    FROM user_interessent 
                    INNER JOIN user_account ON user_interessent.user_id = user_account.user_id 
                    WHERE user_account.loeschung = 1 AND user_account.art_id = 1 AND NOW() >= DATE_ADD(user_account.loesch_datum, INTERVAL 7 DAY)";
        return $this->doQuery($sql, $values);
    }

    public function getBundeslaender(iterable $values=[]) {
        $sql = "SELECT * FROM geo_bundesland";
        return $this->doQuery($sql, $values);
    }

    public function getAllGeschaeftsstelle(iterable $values=[]) {
        $sql = "SELECT * FROM user_geschaeftsstelle";
        return $this->doQuery($sql, $values);
    }

    public function getGeschaeftsstelle(iterable $values=[]) {
        $sql = "SELECT * FROM user_geschaeftsstelle WHERE geschaeftsstelle_id = :geschaeftsstelle_id";
        return $this->doQuery($sql, $values)->fetchAssociative();
    }

    public function insertAccountForStatisticUser(iterable $values=[]){
        $sql = "INSERT INTO user_account SET 
                    art_id                = :art_id, 
                    recht_id              = :recht_id,
                    geschaeftsstellen_id  = :geschaeftsstellen_id,
                    kennwort              = :kennwort,
                    username              = :username,
                    email                 = :email,
                    registrierungsdatum   = :regdate,
                    authentifiziert       = :authentifiziert, 
                    gesperrt              = :gesperrt,
                    loeschung             = :loeschung,
                    newsletter            = :newsletter
                ";

        return $this->doSQL($sql, $values);
    }

    public function insertAccountForSupporter(iterable $values=[]){
        $sql = "INSERT INTO user_account SET 
                    art_id                = :art_id, 
                    recht_id              = :recht_id,
                    kennwort              = :kennwort,
                    username              = :username,
                    email                 = :email,
                    registrierungsdatum   = :regdate,
                    authentifiziert       = :authentifiziert, 
                    gesperrt              = :gesperrt,
                    loeschung             = :loeschung,
                    newsletter            = :newsletter
                ";

        return $this->doSQL($sql, $values);
    }

    public function insertAccountForMakler(iterable $values=[]){
        $sql = "INSERT INTO user_account SET 
                    art_id                = :art_id, 
                    recht_id              = :recht_id,
                    kennwort              = :kennwort,
                    username              = :benutzername,
                    email                 = :email,
                    registrierungsdatum   = :regdate,
                    authentifiziert       = :authentifiziert, 
                    gesperrt              = :gesperrt, 
                    kennwort_plain        = :kennwort_plain
                ";

        return $this->doSQL($sql, $values);
    }

    public function insertMakler(iterable $values=[]){
        $sql =  "INSERT INTO user_makler SET
                    user_id             = :user_id, 
                    mitgliedsnummer     = :mitgliedsnummer, 
                    m_konfig_id         = :m_konfig_id, 
                    geschaeftsstelle_id = :geschaeftsstelle_id, 
                    anrede              = :anrede, 
                    titel               = :titel, 
                    namenstitel         = :namenstitel, 
                    name                = :name, 
                    vorname             = :vorname, 
                    firma               = :firma, 
                    strasse             = :strasse, 
                    plz                 = :plz, 
                    ort                 = :ort, 
                    geodb_laender_id    = :geodb_laender_id, 
                    email               = :email, 
                    telefon             = :telefon, 
                    fax                 = :telefax, 
                    homepage            = :homepage, 
                    seo_url             = :seo_url, 
                    sortierung          = :sortierung,
				    ahu_geo_point	    = GeomFromText('POINT(1 1)'),  
                    bundesland_id       = :bundesland_id
                    ";
                    //mitgliedskategorien = :mkategorien 
        return $this->doSQL($sql, $values);
    }

    public function insertUserMaklerConfig(iterable $values=[]){
        $sql  = "INSERT INTO user_makler_config SET 
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
                    logo_pfad = ''
                ";
        return $this->doSQL($sql, $values);
    }

    public function insertRobotQueue(iterable $values=[]){
        $sql  = "INSERT INTO robot_queue SET 
                    config_server_id  = '5',
                    robot_id          = '1',
                    status            = 'QUEUED',
                    robot_name        = 'FTPUserAdd',
                    parameter1        = :homeftp,
                    parameter2        = 'makler',
                    parameter3        = :user_id,
                    parameter4        = '/bin/false',
                    parameter5        = '/etc/skel',
                    parameter6        = :ftppasswortcrypt,
                    parameter7        = :ftp_benutzer,
                    parameter8        = '/usr/sbin/useradd'
                ";
        return $this->doSQL($sql, $values);
    }

    public function updateMaklerAhuGeoPoint(iterable $values=[]){
        $sql = "UPDATE user_makler SET ahu_geo_point = ST_GeomFromText('Point(0 0)') WHERE user_id = :user_id";
        return $this->doSQL($sql, $values);
    }

    public function getUserAccount(iterable $values=[]){
        $sql = "SELECT username FROM user_account WHERE user_id = :user_id";
        return $this->doQuery($sql, $values)->fetchAssociative();
    }

    public function updateUserAccountEmail(iterable $values=[]){
        $sql  =  "UPDATE user_account SET email = :email WHERE user_id= :user_id";
        return $this->doSQL($sql, $values);
    }

    public function updateUserAccountPW(iterable $values=[]){
        $sql  =  "UPDATE user_account SET kennwort  = :crypt_passwort WHERE user_id = :user_id";
        return $this->doSQL($sql, $values);
    }

    public function updateUserAccountGesperrt(iterable $values=[]){
        $sql = "UPDATE user_account SET gesperrt = :gesperrt WHERE user_id = :user_id";
        return $this->doSQL($sql, $values);
    }

    public function updateUserAccountLoeschung(iterable $values=[]){
        $sql = "UPDATE user_account SET loeschung=0, loesch_send=0 WHERE user_id = :user_id";
        return $this->doSQL($sql, $values);
    }

    public function updateUserInteressent(iterable $values=[]){
        $sql   =   "UPDATE user_interessent 
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
                    mobil               = :mobil
                    WHERE       user_id = :user_id
                    ";
        return $this->doSQL($sql, $values);
    }

    public function deleteInteressent(iterable $values=[]){
        $tabs = array(
            "user_account",
            "user_account_merkliste",
            "user_account_merkliste_experten",
            "user_interessent",
            "user_suchauftraege_neu",
            "objekt_vergleich",
            "outboundcalls",
            "outboundcalls_user_makler",
            "user_suchauftraege_send"
        );
        
        $sql = "";
        foreach ($tabs as $tab) {
            $sql = "DELETE FROM ".$tab." WHERE user_id = :user_id";
            $this->doSQL($sql, $values);
        }

        return true;
    }

}