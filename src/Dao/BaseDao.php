<?php
namespace App\Dao;

use Doctrine\ORM\EntityManagerInterface;
//use App\Location\Entity\Result;

class BaseDao {
    
    public $em;// $em->getConnection() => Doctrine\DBAL\Connection

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function doQuery($sql, $values){
        $stmt = $this->em->getConnection()->prepare($sql);  // Doctrine\DBAL\Statement
        if(!$stmt->execute($values)) {
            //throw new \Exception("DoQuery faild!"); 
        }  
        return $stmt;
        //return $stmt->fetchAll(); $stmt->fetch();
    }

    public function doSQL($sql, $values){
        $stmt = $this->em->getConnection()->prepare($sql);
        if(!$stmt->execute($values)){
            //throw new \Exception("DoSQL faild!");
        } 
        return true;
    }

    public function getEm(){
        return $this->em;
    }

    //---------------

    public function insertSendQueue(iterable $values=[]){
        $sql = "INSERT INTO send_queue 
                SET 
                sendername      = :sendername,
                absender_mail   = :absender_mail,
                empfaenger_name = :empfaenger_name,
                empfaenger_mail = :empfaenger_mail,
                betreff         = :betreff,
                nachricht       = :nachricht_html,
                nachricht_plain = :nachricht_plain,
                insertdate      = :insertdate
                ";
        return $this->doSQL($sql, $values);
    }

    public function getBundeslaender(iterable $values=[]) {
        $sql = "SELECT * FROM geo_bundesland";
        return $this->doQuery($sql, $values)->fetchAll();
    }

    public function getAllGeschaeftsstelle(iterable $values=[]) {
        $sql = "SELECT * FROM user_geschaeftsstelle";
        return $this->doQuery($sql, $values)->fetchAll();
    }

    // public function getGeschaeftsstelle(iterable $values=[]) {
    //     $sql = "SELECT * FROM user_geschaeftsstelle WHERE geschaeftsstelle_id = :geschaeftsstelle_id";
    //     return $this->doQuery($sql, $values)->fetch();
    // }

    public function getRowInTableByIdentifier(string $tabName, iterable $values=[]) {
        foreach($values as $key => $value) {
            $sql = "SELECT * FROM ".$tabName." WHERE $key = $value";
        }
        //return $sql;
        return $this->doQuery($sql, $values)->fetch(); 
    }
}