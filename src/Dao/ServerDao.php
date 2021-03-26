<?php
namespace App\Dao;

class ServerDao extends BaseDao {

    public function getAllServerConfig(iterable $values=[]) {
        $sql    =   "SELECT m.user_id, m.name, m.vorname, m.firma, m.email, config_server.hostname, mc.ftp_benutzer, mc.ftp_passwort, mc.ftp_pause, mc.ftp_import_after_break  
                    FROM user_makler m
                    LEFT JOIN user_makler_config mc ON m.user_id = mc.user_id
                    LEFT JOIN config_server ON mc.ftp_server_id = config_server.config_server_id
                    WHERE m.user_id != 0 AND mc.ftp_pause='N'";
        return $this->doQuery($sql, $values);
    }

    public function getServerOfMakler(iterable $values=[]) {
        $sql    =   "SELECT m.user_id, m.name, m.vorname, m.firma, m.email, config_server.hostname, mc.ftp_benutzer, mc.ftp_passwort, mc.ftp_pause, mc.ftp_import_after_break  
                    FROM user_makler m
                    LEFT JOIN user_makler_config mc ON m.user_id = mc.user_id
                    LEFT JOIN config_server ON mc.ftp_server_id = config_server.config_server_id 
                    WHERE m.user_id = :user_id";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function updateServerConfig(iterable $values=[]){
        $sql  =  "UPDATE user_makler_config 
                  SET 
                  ftp_pause = :ftp_pause,
                  ftp_import_after_break = :ftp_import_after_break 
                  WHERE user_id= :user_id";
        return $this->doSQL($sql, $values);
    }

    public function getAllFtpServerPause(iterable $values=[]) {
        $sql    =   "SELECT m.user_id, m.name, m.vorname, m.firma, m.email, config_server.hostname, mc.ftp_benutzer, mc.ftp_passwort, mc.ftp_pause, mc.ftp_import_after_break  
                    FROM user_makler m
                    LEFT JOIN user_makler_config mc ON m.user_id = mc.user_id
                    LEFT JOIN config_server ON mc.ftp_server_id = config_server.config_server_id
                    WHERE m.user_id != 0 AND mc.ftp_pause='J'";
        return $this->doQuery($sql, $values);
    }
   
}