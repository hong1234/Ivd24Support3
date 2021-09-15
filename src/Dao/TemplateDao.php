<?php
namespace App\Dao;

class TemplateDao extends BaseDao {

    public function getTemplateByCategory(iterable $values=[]) {
        $sql = "SELECT mail_template_id, titel, kategorie_id, DATE_FORMAT(insert_date, '%Y-%m-%d') AS insert_date, document_path FROM send_mail_templates WHERE kategorie_id = :kategorie_id";
        return $this->doQueryObj($sql, $values);
    }

    public function getAllTemplate(iterable $values=[]) {
        $sql = "SELECT mail_template_id, titel, kategorie_id, DATE_FORMAT(insert_date, '%Y-%m-%d') AS insert_date, document_path FROM send_mail_templates";
        return $this->doQueryObj($sql, $values);
    }

    public function insertTemplate(iterable $values=[]){
        $sql = "INSERT INTO send_mail_templates SET
                kategorie_id  = 2, 
                titel         = :templatename,
                nachricht     = :template,
                document_path = :document_path,
                insert_date   = NOW()
                ";
        return $this->doSQL($sql, $values);
    }

    public function updateTemplate(iterable $values=[]){
        $sql = "UPDATE send_mail_templates SET
                titel         = :templatename,
                nachricht     = :template,
                document_path = :document_path
                WHERE mail_template_id = :template_id
                ";
        return $this->doSQL($sql, $values);
    }

    public function deleteTemplate(iterable $values=[]){
        $sql = "DELETE FROM send_mail_templates WHERE mail_template_id = :template_id";
        $this->doSQL($sql, $values);
    }

}