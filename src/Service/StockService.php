<?php
namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Dao\StockDao;

class StockService
{
    private $stockDao;
    private $router;
    private $sqService;

    function __construct(StockDao $stockDao, SendQueue $sqService, UrlGeneratorInterface $router) {
        $this->stockDao = $stockDao;
        $this->router = $router;
        $this->sqService = $sqService;
    }

    public function getStockNumber() {
        $rs = $this->stockDao->getStockNumber();
        return $rs['AnzahlAktienGesamt'];
    }

    public function getStockNumberWithStakeholder() {
        $rs = $this->stockDao->getStockNumberWithStakeholder();
        return $rs['AnzahlAktienMitZuweisungZuStakeholder'];
    }

    public function getStockNumberWithOutStakeholder() {
        $rs = $this->stockDao->getStockNumberWithOutStakeholder();
        return $rs['AnzahlAktienOhneZuweisungZuStakeholder'];
    }

    public function getStockNumberAG() {
        $rs = $this->stockDao->getStockNumberAG();
        return $rs['AnzahlAktienAG'];
    }

    public function getShareholderStruktur() {

        $AnzahlAktienGesamt = $this->getStockNumber();
        $AnzahlAktienAG = $this->getStockNumberAG();
        $AnzahlAktienOhneZuweisungZuStakeholder = $this->getStockNumberWithOutStakeholder();
        $AnzahlAktienMitZuweisungZuStakeholder = $this->getStockNumberWithStakeholder();

        $donutData = array(
            [
                'label' => 'AktienGesamt',
                'value' => $AnzahlAktienGesamt
            ],
            [
                'label' => 'AktienIvd24AG',
                'value' => $AnzahlAktienAG
            ],
            [
                'label' => 'AktienOhneStakeholder',
                'value' => $AnzahlAktienOhneZuweisungZuStakeholder
            ],
            [
                'label' => 'AktienMitStakeholder',
                'value' => $AnzahlAktienMitZuweisungZuStakeholder
            ]
        );

        return $donutData;
    }

    public function getStakeholderStruktur() {
        $rs = array();

        $AnzahlAktienGesamt = $this->getStockNumber();

        $stmt = $this->stockDao->getStakeholderStruktur();
        while ($row = $stmt->fetch()) {
            $aktien_az = $row['aktien_az'];
            $aktien_az_stakeholder = $row['aktien_az_stakeholder'];

            $row2 = array();
            $row2['holder'] = $row['stakeholder'];
            $row2['percent'] = round($aktien_az*100/$AnzahlAktienGesamt, 4);
            $row2['note'] = "$aktien_az Aktien davon $aktien_az_stakeholder im Eigenbesitz";

            $rs[] = $row2;
        }

        $AnzahlAktienOhneZuweisungZuStakeholder = $this->getStockNumberWithOutStakeholder();
        $row2 = array();
        $row2['holder'] = 'Aktien ohne Zuweisung zu Stakeholder';
        $row2['percent'] = round($AnzahlAktienOhneZuweisungZuStakeholder*100/$AnzahlAktienGesamt, 4);
        $row2['note'] = "$AnzahlAktienOhneZuweisungZuStakeholder Aktien";
        $rs[] = $row2;

        $AnzahlAktienAG = $this->getStockNumberAG();
        $row2 = array();
        $row2['holder'] = 'Ivd24 Immobilien AG';
        $row2['percent'] = round($AnzahlAktienAG*100/$AnzahlAktienGesamt, 4);
        $row2['note'] = "$AnzahlAktienAG Aktien";
        $rs[] = $row2;

        $row2 = array();
        $row2['holder'] = 'Total';
        $row2['percent'] = 100;
        $row2['note'] = "$AnzahlAktienGesamt Aktien";
        $rs[] = $row2;

        return $rs;
    }

    public function notShareholderList(){
        $stmt = $this->stockDao->notShareHolderList();
        $rows = array();
        while ($row = $stmt->fetch()) {
            $row2 = array();
            $row2[] = $row['user_id'];
            $row2[] = $row['mitgliedsnummer'];
            $row2[] = $row['vorname'].' '.$row['name'];
            $row2[] = $row['firma']; 
            $row2[] = $row['email'];
            $row2[] = $row['telefon'];
            $row2[] = $row['ort'];
            $row2[] = $row['reg_date'];  
            $row2[] = $row['last_login'];  

            $rows[] = $row2;
        }

        return $rows;
    }

    public function shareholderList() {

        $stmt = $this->stockDao->shareholderList();
        
        $rs = array();
        while ($row = $stmt->fetch()) {
            if($row['aktien_az']>0){
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
        }

        return $rs;
    }

    public function notVerifiedList() {
        $stmt = $this->stockDao->getNotVerifiedAktien();
        $rs = array();
        while ($row = $stmt->fetch()) {
            $row2 = array();
            $row2[] = $row['user_id'];
            $row2[] = $row['mitgliedsnummer'];
            $row2[] = $row['vorname'].' '.$row['name'];
            $row2[] = $row['firma'];
            $row2[] = $row['email'];
            $row2[] = date("Y-m-d", (int)$row['lastlogin']);

            $links = "<a href=".$this->router->generate('stock_verify', array('userid' => $row['user_id'])).">Kauf verifizieren</a><br>";
            $row2[] = $links;

            $rs[] = $row2;
        }

        return $rs;
    }

    public function maklerData($user_id){
        $makler = $this->stockDao->getVeryfiMakler([
            'user_id' => $user_id
        ]);
        return $makler;
    }

    public function maklerAktien($user_id){
        $aktien = $this->stockDao->getNotVerifiedUserAktien([
            'user_id' => $user_id
        ]);
        return $aktien;
    }

    public function aktienDoc($user_id){
        $docs = $this->stockDao->getAktienDoc([
            'user_id' => $user_id
        ]);
        return $docs;
    }

    public function verifyUserAktien(int $user_id){
        $rs = $this->stockDao->updateVerified([
            'user_id' => $user_id
        ]);
        return $rs;
    }

    //--------------

    public function inviteToMeeting($hauptversammlung_id, $safePost){

        $betreff     = $safePost->get('betreff');    // 'betreff' => string 'Ladung zu Sammlung' (length=18)
        $template_id = $safePost->get('template');   // 'template' => string 'temp1' (length=5)

        $stmt = $this->stockDao->getAktionaerToInvite([
            'hauptversammlung_id' => $hauptversammlung_id,
            'mail_template_id'    => $template_id
        ]);

        while ($row = $stmt->fetch()) {
            $this->doInvite($betreff, $hauptversammlung_id,  $template_id, $row);
        }

        $stmt = $this->stockDao->getAktionaerToInvite2([
            'hauptversammlung_id' => $hauptversammlung_id,
            'mail_template_id'    => $template_id
        ]);

        while ($row = $stmt->fetch()) {
            $this->doInvite2($betreff, $hauptversammlung_id, $template_id, $row);
        }

    }

    public function doInvite($betreff, $hauptversammlung_id, $template_id, $row){

        $user_id = (int)$row->user_id;
        $geschaeftsstelle_id = $row->user_geschaeftsstelle_id;
        $email = 'technik@ivd24.de'; //$row->email;
        $name = $row->name;

        $this->stockDao->insertHauptversammlungEmailCommunication([
            'hauptversammlung_id' => $hauptversammlung_id,
            'user_id'             => $user_id,
            'geschaeftsstelle_id' => $geschaeftsstelle_id,
            'mail_template_id'    => $template_id
        ]);

        $this->sqService->addToSendQueue2('mode1', [
            'betreff'     => $betreff,
            'email'       => $email,
            'template_id' => $template_id,
            'name'        => $name
        ]);
    }

    public function doInvite2($betreff, $hauptversammlung_id, $template_id, $row){
        // $user_id = NULL;
        // $region = $row->region;
        $geschaeftsstelle_id = $row->user_geschaeftsstelle_id;
        $email = 'technik@ivd24.de'; //$row->email;
        $name  = $row->name;

        $this->stockDao->insertHauptversammlungEmailCommunication2([
            'hauptversammlung_id' => $hauptversammlung_id,
            'geschaeftsstelle_id' => $geschaeftsstelle_id,
            'mail_template_id'    => $template_id
        ]);

        $this->sqService->addToSendQueue2('mode2', [
            'betreff'     => $betreff,
            'email'       => $email,
            'template_id' => $template_id,
            'name'        => $name
        ]);
    }

}