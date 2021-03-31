<?php
namespace App\Service;  

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Dao\BUserDao;

class BcUserService
{
    private $router;
    public  $bcuDao;

    function __construct(UrlGeneratorInterface $router, BUserDao $bcuDao) {
        $this->router = $router;
        $this->bcuDao = $bcuDao;
    }

    public function BcUserList(){

        $stmt = $this->bcuDao->getAllBUser();

        $rows = array();
        while ($row = $stmt->fetch()) {
            $row2 = array();

            $row2[] = $row['user_id'];
            $row2[] = $row['returncode'];    // string 'info@kaiser-immobilien.de' (length=25)
            $row2[] = $row['company_name'];  // string 'Kaiser Immobilien GmbH & Co. KG' (length=31)

            $status   = 'nicht-bz';
            if($row['paid'] == 1){
                $status = 'bezahlt';
            }
            $row2[] = $status;

            $row2[] = $row['paket_name'];               // string 'ivd24 Business-Club + StoryBox' (length=30)
            $row2[] = substr($row['start_abo'], 0, 10); // string '2021-02-01 00:00:01' (length=19) ;
            $row2[] = substr($row['end_abo'], 0, 10);   // string '2022-01-31 23:59:59' (length=19)

            $row2[] = "<a href=".$this->router->generate('bcuser_edit', array('uid' => $row['user_id'])).">Daten bearbeiten</a><br>";  

            $rows[] = $row2;
        }

        return $rows;
    }

    public function BcUserDelList(){

        $stmt = $this->bcuDao->getAllBUser();

        $rows = array();
        while ($row = $stmt->fetch()) { 
            $row2 = array();

            $row2[] = $row['user_id'];
            $row2[] = $row['returncode'];    // string 'info@kaiser-immobilien.de' (length=25)
            $row2[] = $row['company_name'];  // string 'Kaiser Immobilien GmbH & Co. KG' (length=31)

            $status   = 'nicht-bz';
            if($row['paid'] == 1){
                $status = 'bezahlt';
            }
            $row2[] = $status;
            $row2[] = $row['paket_name'];                   // string 'ivd24 Business-Club + StoryBox' (length=30)
            $row2[] = substr($row['start_abo'], 0, 10);     // string '2021-02-01 00:00:01' (length=19) ;
            $row2[] = substr($row['end_abo'], 0, 10);       // string '2022-01-31 23:59:59' (length=19)
            $row2[] = "<a href=".$this->router->generate('bcuser_delete', array('uid' => $row['user_id'])).">BcUser löschen</a><br>";
            
            $rows[] = $row2;
        }

        return $rows;
    }
}