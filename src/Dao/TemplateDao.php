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

    public function insert1Template(iterable $values=[]){
        $sql = "INSERT INTO send_mail_templates SET
                kategorie_id  = 2, 
                titel         = :templatename,
                nachricht     = :template,
                document_path = :document_path,
                insert_date   = CURDATE()
                ";
        return $this->doSQL($sql, $values);
    }

    public function templatesByCategory2(){

        // $stmt = $this->getTemplateByCategory([
        //     'kategorie_id' => '2'
        // ]);

        $stmt = $this->getAllTemplate();

        $rows = array();
        while ($row = $stmt->fetch()) {

            $row2 = array();
            $row2[] = (int)$row->mail_template_id;
            $row2[] = (int)$row->kategorie_id;
            $row2[] = $row->titel;
            if(strlen($row->document_path)){
                $row2[] = 'Ja';
            } else {
                $row2[] = 'Nein';
            }
            $row2[] = $row->insert_date;
            $row2[] = 'links';
            $rows[] = $row2;
        }

        return $rows;
    }

    public function insertTemplate($templatename, $template, $dokument){
        $this->insert1Template([
            'templatename'  => $templatename,
            'template'      => $template,
            'document_path' => $dokument
        ]);
    }
}