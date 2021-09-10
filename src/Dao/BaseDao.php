<?php
namespace App\Dao;

// use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Driver\Connection;
use App\Entity\Result;

class BaseDao {
    
    // public $em;// $em->getConnection() => Doctrine\DBAL\Connection

    // public function __construct(EntityManagerInterface $em) {
    //     $this->em = $em;
    // }

    // public function doQuery($sql, $values){
    //     $stmt = $this->em->getConnection()->prepare($sql);  // Doctrine\DBAL\Statement
    //     if(!$stmt->execute($values)) {
    //         //throw new \Exception("DoQuery faild!"); 
    //     }  
    //     return $stmt;
    //     //return $stmt->fetchAll(); $stmt->fetch();
    // }

    // public function doSQL($sql, $values){
    //     $stmt = $this->em->getConnection()->prepare($sql);
    //     if(!$stmt->execute($values)){
    //         //throw new \Exception("DoSQL faild!");
    //     } 
    //     return true;
    // }

    // public function getEm(){
    //     return $this->em;
    // }

    //---------------

    private $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    public function doQuery($sql, $values){
        $stmt = $this->connection->prepare($sql);  // Doctrine\DBAL\Statement
        if(!$stmt->execute($values)) {
            //throw new \Exception("DoQuery faild!"); 
        }  
        return $stmt;
        // $stmt->fetch();
        // return $stmt->fetchAll(); 
    }

    public function doQueryObj($sql, $values){
        $stmt = $this->connection->prepare($sql);
        if(!$stmt->execute($values)){
            // throw new \Exception("Query faild!");
        } 

        $stmt->setFetchMode(\PDO::FETCH_CLASS, Result::class);
        
        return $stmt;
        //return $stmt->fetch(); 
        //return $stmt->fetchAll();
    }

    public function doSQL($sql, $values){
        $stmt = $this->connection->prepare($sql);
        if(!$stmt->execute($values)){
            //throw new \Exception("DoSQL faild!");
        } 
        return true;
    }

    public function dbConnection(){
        return $this->connection;
    }

    //---------------

    public function insertSendQueue(iterable $values=[]){
        $sql = "INSERT INTO send_queue SET 
                sendername      = :sendername,
                absender_mail   = :absender_mail,
                reply_mail      = :reply_mail,
                empfaenger_name = :empfaenger_name,
                empfaenger_mail = :empfaenger_mail,
                betreff         = :betreff,
                nachricht       = :nachricht_html,
                nachricht_plain = :nachricht_plain,
                insertdate      = :insertdate
                ";
        return $this->doSQL($sql, $values);
    }

    public function getAllRowsInTable(string $tabName) {
        $sql = "SELECT * FROM ".$tabName;
        return $this->doQuery($sql, [])->fetchAll();
    }

    public function getAllRowsInTable2(string $tabName) {
        $sql = "SELECT * FROM ".$tabName;
        return $this->doQueryObj($sql, [])->fetchAll();
    }

    public function getRowsInTableByPropeties(string $tabName, iterable $values=[]) {
        $index = 0;
        $sql = "SELECT * FROM $tabName WHERE ";
        foreach($values as $key => $value) {
            if($index == 0){
                $sql = $sql."$key = '$value'";
            } else {
                $sql = $sql." AND $key = '$value'";
            }
            $index++;
        }
        //return $sql;
        return $this->doQuery($sql, $values)->fetchAll(); 
    }

    public function getRowInTableByIdentifier(string $tabName, iterable $values=[]) {
        $sql = "SELECT * FROM $tabName WHERE ";
        foreach($values as $key => $value) {
            $sql = $sql."$key = '$value'";
        }
        return $this->doQuery($sql, $values)->fetch(); 
    }

    public function getRowInTableByIdentifier2(string $tabName, iterable $values=[]) {
        $sql = "SELECT * FROM $tabName WHERE ";
        foreach($values as $key => $value) {
            $sql = $sql."$key = '$value'";
        }
        return $this->doQueryObj($sql, $values)->fetch(); 
    }

}