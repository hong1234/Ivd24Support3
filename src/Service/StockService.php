<?php
namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Dao\StockDao;

class StockService
{
    private $stockDao;
    private $router;

    function __construct(StockDao $stockDao, UrlGeneratorInterface $router) {
        $this->stockDao = $stockDao;
        $this->router = $router;
    }

    public function getStockNumber() {
        $rs = $this->stockDao->getStockNumber();
        return $rs['AnzahlAktienGesamt'];
    }

    public function getStockNumberAG() {
        $rs = $this->stockDao->getStockNumberAG();
        return $rs['AnzahlAktienAG'];
    }

    public function getStockNumberWithOutStakeholder() {
        $rs = $this->stockDao->getStockNumberWithOutStakeholder();
        return $rs['AnzahlAktienOhneZuweisungZuStakeholder'];
    }

    public function getStockNumberWithStakeholder() {
        $rs = $this->stockDao->getStockNumberWithStakeholder();
        return $rs['AnzahlAktienMitZuweisungZuStakeholder'];
    }

    public function shareholderList() {

        $stmt = $this->stockDao->shareholderList();
        
        $rs = array();
        while ($row = $stmt->fetch()) {
            
            $row2 = array();
            $row2[] = $row['user_id'];
            $row2[] = $row['mitgliedsnummer'];
            $row2[] = $row['vorname'].' '.$row['name'];
            $row2[] = $row['firma'];
            $row2[] = $row['email'];
            $row2[] = $row['aktien_az'];
            if($row['user_id_stakeholder'] == NULL){
                $row2[] = '--';
            } else {
                $row2[] = $row['stakeholder'];
            }
            
            $links = "links";
            // $links = "<a href=".$this->router->generate('makler_edit', array('uid' => $row['userId'])).">Aktienkaufvertrag</a><br>";
            // $links = $links."<a href=".$this->router->generate('makler_ftp_edit', array('uid' => $row['userId'])).">Rechnung zum Aktienkauf</a><br>";
            
            $row2[] = $links;

            $rs[] = $row2;
        }

        return $rs;
    }

}