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
        //return $stmt->fetchAllAssociative(); $stmt->fetchAssociative();
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
}