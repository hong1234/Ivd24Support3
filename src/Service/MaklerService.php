<?php
namespace App\Service;

use App\Dao\MaklerDao;

class MaklerService
{
    public $mDao;

    function __construct(MaklerDao $mDao) {
        $this->mDao = $mDao;  
    }

    // function getAllMaklerUser() {
    //     return $this->mDao->getAllMaklerUser([]);
    // }
}