<?php
namespace App\Dao;

class BUserDao extends BaseDao {

    public function getAllBUser(iterable $values=[]) {
        $sql    =  "SELECT user_id, returncode, company_name, paid, paket_name, start_abo, end_abo 
                    FROM businessClubUser 
                    LEFT JOIN businessClubPakete ON businessClubUser.paketid = businessClubPakete.paket_id";
        return $this->doQuery($sql, $values);
    }

    public function getBUser(iterable $values=[]) {
        $sql = "SELECT b.user_id AS userId, date_of_registration AS buchungsdatum, name, vorname, firma, user_makler.seo_url, ip_adresse, start_abo, end_abo, total_amount, paketid, paid, returncode AS email, mobile_devices_storybox AS geraete, grundriss_count, used_grundriss_count, end_grundriss, grundriss_voucher 
                FROM businessClubUser b 
                LEFT JOIN businessClubPakete ON b.paketid = businessClubPakete.paket_id 
                LEFT JOIN user_makler ON b.user_id = user_makler.user_id 
                WHERE b.user_id = :user_id";

        return $this->doQuery($sql, $values)->fetchAssociative();
    }

    public function updateBUser(iterable $values=[]) {
        $sql     = "UPDATE businessClubUser 
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
                    WHERE           user_id = :user_id";

        return $this->doSQL($sql, $values);
    }

    public function deleteBUser(iterable $values=[]){
        $sql = "DELETE FROM businessClubUser WHERE user_id = :user_id";
        return $this->doSQL($sql, $values);
    }

}