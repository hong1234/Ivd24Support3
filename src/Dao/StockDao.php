<?php
namespace App\Dao;

class StockDao extends BaseDao {

    public function getStockNumber(iterable $values=[]) {
        $sql = "SELECT count(aktien_id) AS AnzahlAktienGesamt FROM aktien";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getStockNumberWithStakeholder(iterable $values=[]) {
        $sql = "SELECT count(aktien.user_id) AS AnzahlAktienMitZuweisungZuStakeholder 
                FROM aktien 
                LEFT JOIN user_account ON user_account.user_id = aktien.user_id 
                WHERE aktien.user_id IS NOT NULL AND user_account.user_id_stakeholder IS NOT NULL";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getStockNumberWithOutStakeholder(iterable $values=[]) {
        $sql = "SELECT count(aktien.user_id) AS AnzahlAktienOhneZuweisungZuStakeholder 
                FROM aktien 
                LEFT JOIN user_account ON user_account.user_id = aktien.user_id 
                WHERE aktien.user_id IS NOT NULL AND user_account.user_id_stakeholder IS NULL";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getStockNumberAG(iterable $values=[]) {
        $sql = "SELECT count(aktien_id) AS AnzahlAktienAG FROM aktien WHERE user_id IS NULL";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getStakeholderStruktur(iterable $values=[]){
        $sql = "SELECT  user_stakeholder.user_id AS user_id_stakeholder, user_stakeholder.stakeholderinfos AS stakeholder, count(aktien.aktien_id) AS aktien_az, (SELECT count(aktien.aktien_id) FROM aktien WHERE aktien.user_id = user_account.user_id_stakeholder) AS aktien_az_stakeholder 
                FROM aktien 
                LEFT JOIN user_account ON user_account.user_id = aktien.user_id 
                LEFT JOIN user_stakeholder ON user_stakeholder.user_id = user_account.user_id_stakeholder 
                WHERE aktien.user_id IS NOT NULL AND user_account.user_id_stakeholder IS NOT NULL 
                GROUP BY user_account.user_id_stakeholder";
        return $this->doQuery($sql, $values);
    }

    public function shareHolderList(iterable $values=[]){
        // $sql = "SELECT user_makler.user_id, user_makler.mitgliedsnummer, user_makler.vorname, user_makler.name, user_makler.firma, user_makler.email,
        //         (SELECT count(aktien.user_id) FROM aktien WHERE aktien.user_id = user_makler.user_id) AS aktien_az,
        //         user_account.user_id_stakeholder, user_stakeholder.stakeholderinfos AS stakeholder, aktien_documents.aktien_document_id AS aktien_doc_id, aktien_documents.document_name
        //         FROM user_makler
        //         LEFT JOIN user_account ON user_account.user_id = user_makler.user_id
        //         LEFT JOIN user_stakeholder ON user_stakeholder.user_id = user_account.user_id_stakeholder
        //         LEFT JOIN aktien_documents ON aktien_documents.user_id = user_makler.user_id
        //         WHERE user_account.art_id = 2 AND user_account.recht_id = 3
        //         ";

        $sql = "SELECT user_makler.user_id, user_makler.mitgliedsnummer, user_makler.vorname, user_makler.name, user_makler.firma, user_makler.email, akt.aktien_az, 
                user_stakeholder.stakeholder_id, user_stakeholder.stakeholderinfos AS stakeholder, aktien_documents.aktien_document_id, aktien_documents.document_name
                FROM (SELECT aktien.user_id, count(aktien.user_id) AS aktien_az FROM aktien GROUP BY aktien.user_id) AS akt
                INNER JOIN user_makler ON user_makler.user_id = akt.user_id
                INNER JOIN user_account ON user_account.user_id = user_makler.user_id
                LEFT JOIN user_stakeholder ON user_stakeholder.stakeholder_id = user_account.user_id_stakeholder
                LEFT JOIN aktien_documents ON aktien_documents.user_id = user_makler.user_id
                WHERE user_account.art_id = 2 AND user_account.recht_id = 3
                ORDER BY user_makler.user_id ASC
                ";

        return $this->doQueryObj($sql, $values);
    }

    public function notShareHolderList(iterable $values=[]){
        $sql = "SELECT m.user_id, m.mitgliedsnummer, m.vorname, m.name, m.firma, m.email, m.telefon, m.ort, DATE_FORMAT(user_account.created_at, '%Y-%m-%d') AS reg_date, DATE_FORMAT(user_account.last_login_at, '%Y-%m-%d') AS last_login
                FROM user_makler m
                INNER JOIN user_account ON user_account.user_id = m.user_id
                LEFT JOIN (SELECT * FROM aktien WHERE aktien.user_id IS NOT NULL GROUP BY aktien.user_id) AS ak  ON ak.user_id = m.user_id
                WHERE m.user_id != 0 AND user_account.art_id = 2 AND user_account.recht_id = 3 AND ak.user_id IS NULL";
        return $this->doQuery($sql, $values);
    }

    //---------

    public function getStakeholders(iterable $values=[]){
        $sql = "SELECT * FROM user_account WHERE art_id = 5";
        return $this->doQuery($sql, $values)->fetchAll();
    }

    public function getMembersOfStakeholder(iterable $values=[]){
        $sql = "SELECT * FROM user_account WHERE user_id_stakeholder = :user_id_stakeholder";
        return $this->doQuery($sql, $values)->fetchAll();
    }

    public function getAktienAnzahlOfUser(iterable $values=[]){
        $sql = "SELECT count(aktien_id) AS aktien_az FROM aktien WHERE user_id = :user_id";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function getAktienAnzahlOfStakeholder(iterable $values=[]){
        $sql = "SELECT count(aktien.aktien_id) AS aktien_az 
                FROM aktien 
                LEFT JOIN user_account ON user_account.user_id = aktien.user_id 
                WHERE user_account.user_id_stakeholder = :user_id_stakeholder";
        return $this->doQuery($sql, $values)->fetch();
    }

    // public function getStakeholderStruktur2(iterable $values=[]){
    //     $sql = "SELECT count(aktien.aktien_id) as asd, user_stakeholder.stakeholderinfos 
    //             FROM aktien 
    //             LEFT JOIN user_account ON user_account.user_id = aktien.user_id 
    //             LEFT JOIN user_stakeholder ON user_stakeholder.user_id = user_account.user_id_stakeholder 
    //             WHERE aktien.user_id IS NOT NULL AND user_account.user_id_stakeholder IS NOT NULL 
    //             GROUP BY user_account.user_id_stakeholder";
    //     return $this->doQuery($sql, $values)->fetchAll();
    // }

    //---------------

    // eine Liste aller noch nicht verifizierten Aktienkäufe
    public function getNotVerifiedAktien(iterable $values=[]){
        $sql = "SELECT aktien.user_id, user_makler.mitgliedsnummer,  user_makler.vorname, user_makler.name, user_makler.firma, user_makler.email, user_account.lastlogin 
                FROM aktien
                LEFT JOIN user_makler ON user_makler.user_id = aktien.user_id
                LEFT JOIN user_account ON user_account.user_id = user_makler.user_id
                WHERE aktien.purchase_date IS NOT NULL AND aktien.purchase_verified = 0 
                GROUP BY aktien.user_id";
        return $this->doQuery($sql, $values);
    }

    // noch nicht verifizierten Aktienkäufe von dem user
    public function getNotVerifiedUserAktien(iterable $values=[]){
        $sql = "SELECT aktien_id, aktienhash, creation_date, purchase_date
                FROM aktien
                WHERE aktien.purchase_date IS NOT NULL AND aktien.purchase_verified = 0 AND aktien.user_id = :user_id";
        return $this->doQuery($sql, $values)->fetchAll();
    }

    public function getVeryfiMakler(iterable $values=[]){
        $sql = "SELECT user_makler.user_id, user_makler.mitgliedsnummer, user_makler.firma, user_makler.vorname, user_makler.name, user_makler.strasse, user_makler.plz, user_makler.ort, user_makler.email, user_makler.telefon, 
                (SELECT count(aktien.user_id) FROM aktien WHERE aktien.user_id = user_makler.user_id) AS aktien_az
                FROM user_makler
                WHERE user_makler.user_id = :user_id";
        return $this->doQuery($sql, $values)->fetch();
    }

    public function updateVerified(iterable $values=[]){
        $sql  = "UPDATE aktien SET purchase_verified = 1 WHERE user_id = :user_id";
        return $this->doSQL($sql, $values);
    }

    public function getAktienAnzahlByUserId(iterable $values=[]){
        $sql = "SELECT count(*) AS ak_anzahl FROM aktien WHERE purchase_date IS NOT NULL AND user_id = :user_id";
        return $this->doQuery($sql, $values)->fetch();
    }

    //-------------------
    public function deleteDocByDocId(iterable $values=[]){
        $sql = "DELETE FROM aktien_documents WHERE aktien_document_id = :aktien_document_id";
        $this->doSQL($sql, $values);
    }

    public function getAktienDocByUserIdAndCategory(iterable $values=[]){
        $sql = "SELECT * FROM aktien_documents  WHERE user_id = :user_id AND document_cateogory = :document_cateogory";
        return $this->doQueryObj($sql, $values)->fetchAll();
    }

    public function getAktienDocByDocId(iterable $values=[]){
        $sql = "SELECT * FROM aktien_documents WHERE aktien_document_id = :aktien_document_id";
        return $this->doQueryObj($sql, $values)->fetch();
    }

    public function getAktienDoc(iterable $values=[]){
        $sql = "SELECT * FROM aktien_documents WHERE user_id = :user_id";
        return $this->doQuery($sql, $values)->fetchAll();
    }

    public function insertAktienDoc(iterable $values=[]){
        $sql = "INSERT INTO aktien_documents SET
                user_id = :user_id,
                document_cateogory = :document_cateogory,
                document_name = :document_name,
                document_info = :document_info,
                document_path = :document_path
                ";
        return $this->doSQL($sql, $values);
    }

    //----------------------------------------------------------
    // public function getSendMailTempleteStory(iterable $values=[]) {
    //     $sql = "SELECT hauptversammlung_email_communication.insert_date, send_mail_templates.titel FROM hauptversammlung_email_communication 
    //             LEFT JOIN send_mail_templates ON send_mail_templates.mail_template_id = hauptversammlung_email_communication.send_mail_template_id
    //             GROUP BY hauptversammlung_email_communication.send_mail_template_id, hauptversammlung_email_communication.insert_date 
    //             ORDER BY hauptversammlung_email_communication.insert_date DESC";
    //     return $this->doQueryObj($sql, $values)->fetchAll();
    // }

    public function getSendMailTempleteStory(iterable $values=[]) {
        $sql = "SELECT DATE_FORMAT(hauptversammlung_email_communication.insert_date, '%Y.%m.%d') AS send_date, send_mail_templates.titel AS send_template 
                FROM hauptversammlung_email_communication 
                LEFT JOIN send_mail_templates ON send_mail_templates.mail_template_id = hauptversammlung_email_communication.send_mail_template_id
                GROUP BY send_date, hauptversammlung_email_communication.send_mail_template_id 
                ORDER BY hauptversammlung_email_communication.insert_date DESC";
        return $this->doQueryObj($sql, $values)->fetchAll();
    }

    public function getAllMeetings() {
        $sql = "SELECT * FROM hauptversammlung ORDER BY meeting_date DESC";
        return $this->doQueryObj($sql, [])->fetchAll();
    }

    public function updateProtokoll(iterable $values=[]){
        $sql  = "UPDATE hauptversammlung SET protocol_general_meeting = :meeting_protocol WHERE id = :meeting_id";
        return $this->doSQL($sql, $values);
    }

    public function updateClosed(iterable $values=[]){
        $sql  = "UPDATE hauptversammlung SET closed = 1 WHERE id = :meeting_id";
        return $this->doSQL($sql, $values);
    }

    public function getTemplatesForGeneralMeeting(iterable $values=[]){
        $sql = "SELECT * FROM send_mail_templates WHERE kategorie_id = 2";
        return $this->doQueryObj($sql, $values)->fetchAll();
    }

    public function getAktionaerToInvite(iterable $values=[]){ // good
        $sql = "SELECT aktien.user_id, aktien.user_geschaeftsstelle_id, user_makler.email, user_makler.anrede, user_makler.vorname, user_makler.name, user_makler.firma
                FROM aktien
                LEFT JOIN user_makler ON user_makler.user_id = aktien.user_id 
                WHERE aktien.user_id IS NOT NULL AND aktien.user_geschaeftsstelle_id IS NOT NULL AND aktien.purchase_verified = 1 
                AND aktien.user_id NOT IN (SELECT com.user_id FROM hauptversammlung_email_communication com WHERE com.hauptversammlung_id = :hauptversammlung_id AND com.send_mail_template_id = :mail_template_id AND com.user_id IS NOT NULL)
                GROUP BY aktien.user_id";
        return $this->doQueryObj($sql, $values);
    }

    public function getAktionaerToInvite2(iterable $values=[]){
        $sql = "SELECT aktien.user_geschaeftsstelle_id,  user_geschaeftsstelle.region, user_geschaeftsstelle.email, user_geschaeftsstelle.name
                FROM aktien 
                LEFT JOIN user_geschaeftsstelle ON user_geschaeftsstelle.geschaeftsstelle_id = aktien.user_geschaeftsstelle_id
                WHERE aktien.user_id IS NULL AND aktien.user_geschaeftsstelle_id IS NOT NULL AND aktien.purchase_verified = 1 
                AND aktien.user_geschaeftsstelle_id NOT IN (SELECT com.geschaeftsstelle_id FROM hauptversammlung_email_communication com WHERE com.hauptversammlung_id = :hauptversammlung_id AND com.send_mail_template_id = :mail_template_id AND com.user_id IS NULL)
                GROUP BY aktien.user_geschaeftsstelle_id";
        return $this->doQueryObj($sql, $values);
    }

    public function insertHauptversammlungEmailCommunication(iterable $values=[]){
        $sql = "INSERT INTO hauptversammlung_email_communication SET 
                hauptversammlung_id = :hauptversammlung_id,
                user_id = :user_id,
                geschaeftsstelle_id = :geschaeftsstelle_id,
                send_mail_template_id = :mail_template_id,
                registration_link = 'abc', 
                insert_date = NOW()";
        return $this->doSQL($sql, $values);
    }

    public function insertHauptversammlungEmailCommunication2(iterable $values=[]){
        $sql = "INSERT INTO hauptversammlung_email_communication SET 
                hauptversammlung_id = :hauptversammlung_id,
                geschaeftsstelle_id = :geschaeftsstelle_id,
                send_mail_template_id = :mail_template_id,
                registration_link = 'abc',  
                insert_date = NOW()";
        return $this->doSQL($sql, $values);
    }

}