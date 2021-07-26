<?php
namespace App\Dao;

class BUserDao extends BaseDao {

    public function getAllNotBUser(iterable $values=[]){
        $sql = "SELECT m.user_id, m.mitgliedsnummer, m.vorname, m.name, m.firma, m.email, DATE_FORMAT(a.created_at, '%Y-%m-%d') AS reg_date, DATE_FORMAT(a.last_login_at, '%Y-%m-%d') AS last_login
                FROM user_makler m
                INNER JOIN user_account a ON  m.user_id = a.user_id
                LEFT JOIN businessClubUser ON m.user_id = businessClubUser.user_id
                WHERE a.art_id = 2 AND a.recht_id = 3 AND m.user_id !=0 AND businessClubUser.user_id IS NULL";
        return $this->doQuery($sql, $values);
    }

    public function getAllBUser(iterable $values=[]) {
        $sql = "SELECT businessClubUser.user_id, user_makler.mitgliedsnummer, returncode, company_name, paid, paket_name, start_abo, end_abo 
                FROM businessClubUser 
                INNER JOIN user_makler ON user_makler.user_id = businessClubUser.user_id
                LEFT JOIN businessClubPakete ON businessClubUser.paketid = businessClubPakete.paket_id";
        return $this->doQuery($sql, $values);
    }

    public function getBUser(iterable $values=[]) {
        $sql = "SELECT b.user_id AS userId, date_of_registration AS buchungsdatum, name, vorname, firma, user_makler.seo_url, ip_adresse, start_abo, end_abo, total_amount, paketid, paid, returncode AS email, mobile_devices_storybox AS geraete, grundriss_count, used_grundriss_count, end_grundriss, grundriss_voucher 
                FROM businessClubUser b 
                LEFT JOIN businessClubPakete ON b.paketid = businessClubPakete.paket_id 
                LEFT JOIN user_makler ON b.user_id = user_makler.user_id 
                WHERE b.user_id = :user_id";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function updateBUser(iterable $values=[]) {
        $sql = "UPDATE businessClubUser 
                SET
                user_id                 = :userid, 
                date_of_registration    = :buchungsdatum,
                company_name            = :firma,
                seo_url                 = :seo_url,
                start_abo               = :start_abo,
                end_abo                 = :end_abo,
                total_amount            = :total_amount, 
                paketid                 = :paket_id,
                paid                    = :paid,
                returncode              = :email,
                end_grundriss           = :end_grundriss,
                grundriss_voucher       = :grundriss_voucher,
                mobile_devices_storybox = :geraete
                WHERE user_id = :user_id";

        return $this->doSQL($sql, $values);
    }

    public function deleteBUser(iterable $values=[]){
        $sql = "DELETE FROM businessClubUser WHERE user_id = :user_id";
        return $this->doSQL($sql, $values);
    }

}