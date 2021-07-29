<?php
namespace App\Service;

use App\Service\TempFileHandler;

use App\Dao\StatisticDao;
use App\Dao\ObjectDao;

class StatisticService
{
    private $sDao;
    private $oDao;
    private $tempFileHandler;
    
    public function __construct(StatisticDao $sDao, ObjectDao $oDao, TempFileHandler $tempFileHandler) {
        $this->sDao = $sDao;
        $this->oDao = $oDao;
        $this->tempFileHandler = $tempFileHandler; 
    }

    public function geschaeftsstelleId(int $user_id) {
        $ac = $this->sDao->getUserGeschaeftsstelle([
            'user_id' => $user_id
        ]);

        $geschaeftsstelle_id = (int)$ac['geschaeftsstellen_id'];
        return $geschaeftsstelle_id;
    }

    public function statisticMakler(int $geschaeftsstelle_id) {

        $rows1 = array();
        if($geschaeftsstelle_id == 6) { // all maklers
            
            $stmt = $this->sDao->getMaklerProRegion();

            $rows1[] = [
                'name' => 'IVD Süd',
                'geschaeftsstelle_id' => 1,
                'count_makler_on_regional_office' => 0
            ];

            while($row = $stmt->fetch()) {
                if((int)$row['geschaeftsstelle_id'] == 1 || (int)$row['geschaeftsstelle_id'] == 2 ){
                    $rows1[0]['count_makler_on_regional_office'] = $rows1[0]['count_makler_on_regional_office'] + $row['count_makler_on_regional_office'];
                } else {
                    $rows1[] = $row;
                }
            }

        } elseif ($geschaeftsstelle_id == 1 || $geschaeftsstelle_id == 2) {

            $reg1 = $this->sDao->getMaklerOnRegion(['geschaeftsstelle_id' => 1]);
            $reg2 = $this->sDao->getMaklerOnRegion(['geschaeftsstelle_id' => 2]);

            $row = [
                'name' => 'IVD Süd',
                'geschaeftsstelle_id' => $geschaeftsstelle_id,
                'count_makler_on_regional_office' => $reg1['count_makler_on_regional_office'] + $reg2['count_makler_on_regional_office']
            ];

            $rows1[] = $row;

        } else {

            $row = $this->sDao->getMaklerOnRegion(['geschaeftsstelle_id' => $geschaeftsstelle_id]);
            $rows1[] = $row;
        }

        //------
        $rows2 = array();
        if($geschaeftsstelle_id == 6){
            
            $stmt = $this->sDao->getMaklerHaveObjectProRegion();

            $rows2[] = [
                'name' => 'IVD Süd',
                'geschaeftsstelle_id' => 1,
                'count_makler_with_aktive_objectdata' => 0
            ];

            while($row = $stmt->fetch()) {
                if((int)$row['geschaeftsstelle_id'] == 1 || (int)$row['geschaeftsstelle_id'] == 2){
                    $rows2[0]['count_makler_with_aktive_objectdata'] = $rows2[0]['count_makler_with_aktive_objectdata'] + $row['count_makler_with_aktive_objectdata'];
                } else {
                    $rows2[] = $row;
                }
            }

        } elseif ($geschaeftsstelle_id == 1 || $geschaeftsstelle_id == 2) {

            $params = [
                'geschaeftsstelle_id1' => 1,
                'geschaeftsstelle_id2' => 2
            ];

            $reg1 = $this->sDao->getMaklerHaveObjectOnRegion12($params);
           
            $row = [
                'name' => 'IVD-Süd',
                'geschaeftsstelle_id' => $geschaeftsstelle_id,
                'count_makler_with_aktive_objectdata' => $reg1['count_makler_with_aktive_objectdata']
            ];

            $rows2[] = $row;

        } else {

            $row = $this->sDao->getMaklerHaveObjectOnRegion(['geschaeftsstelle_id' => $geschaeftsstelle_id]);
            $rows2[] = $row;
        }

        // result ---------------
        
        $rows = array();
        $i = 0;
        while ($i < count($rows1)) {
            
            $a = $rows1[$i];
            $b = $rows2[$i];

            $row = [
                'name' => $a['name'],
                'geschaeftsstelle_id' => $a['geschaeftsstelle_id'],
                'count_makler_on_regional_office' => $a['count_makler_on_regional_office'],
                'percent' => round(100*$b['count_makler_with_aktive_objectdata']/$a['count_makler_on_regional_office'], 2)
            ];

            $rows[] = $row;
      
            $i++;
        }
        
        return $rows;
    }

    public function statisticObject(int $geschaeftsstelle_id) {

        if($geschaeftsstelle_id == 6) {

            $total = $this->oDao->getObjectTotal()['Anzah_Gesamtl_Objekte'];
            $activ = $this->oDao->getObjectActiv()['Anzahl_freigegeben_Objekte'];
            $inact = $this->oDao->getObjectInActiv()['Anzahl_nicht_freigegeben_Objekte'];

        } elseif ($geschaeftsstelle_id == 1 || $geschaeftsstelle_id == 2){

            $params = [
                'geschaeftsstelle_id1' => 1,
                'geschaeftsstelle_id2' => 2
            ];
            $total = $this->oDao->getObjectTotal12($params)['Anzah_Gesamtl_Objekte'];
            $activ = $this->oDao->getObjectActiv12($params)['Anzahl_freigegeben_Objekte'];
            $inact = $this->oDao->getObjectInActiv12($params)['Anzahl_nicht_freigegeben_Objekte'];

        } else {

            $pars = [
                'geschaeftsstelle_id' => $geschaeftsstelle_id
            ];
            $total = $this->oDao->getObjectTotal2($pars)['Anzah_Gesamtl_Objekte'];
            $activ = $this->oDao->getObjectActiv2($pars)['Anzahl_freigegeben_Objekte'];
            $inact = $this->oDao->getObjectInActiv2($pars)['Anzahl_nicht_freigegeben_Objekte'];
        }

        return ['total'=>$total, 'activ'=>$activ, 'inact'=>$inact];
    }

    public function getDonutData(int $geschaeftsstelle_id) {

        $rs = $this->statisticObject($geschaeftsstelle_id);

        $donutData = array(
            [
                'label' => 'Gesamtl Objekte',
                'value' => $rs['total']
            ],
            [
                'label' => 'freigegeben Objekte',
                'value' => $rs['activ']
            ],
            [
                'label' => 'nicht freigegeben Objekte',
                'value' => $rs['inact']
            ]
        );

        return $donutData;
    }

    public function getAreaData(int $geschaeftsstelle_id) {

        $rs = $this->statisticObject($geschaeftsstelle_id);

        $result = [];
        $now = new \DateTime();
        // $begin = new \DateTime();
        // $begin->modify('-4 week');

        for ($i = 0; $i < 12; $i++) {

            if($i>0){
                $now->modify('-4 week');
                // $begin->modify('-4 week');
            }

            $temp = [
                'day' => $now->format('Y-m-d'),
                // 'begin' => $begin->format('Y-m-d')
                'gesamt' => $rs['total'],
                'frei'   => $rs['activ'],
                'nfrei'  => $rs['inact']
            ];

            $result[] = $temp;
        }
        
        $areaData = $result;

        return $areaData;
    }

    //--------------

    public function objectRequestPeriod(int $geschaeftsstelle_id, \DateTime $begin, \DateTime $now) {
        $temp = [
            'now' => $now->format('Y-m-d'),
            'begin' => $begin->format('Y-m-d')
        ];

        $now_str = $now->format('Y-m-d H:i:s');
        $begin_str = $begin->format('Y-m-d H:i:s');

        if($geschaeftsstelle_id == 6) {

            $rs = $this->sDao->getRequestTimePeriod([
                'beginpoint' => $begin_str,
                'endepoint' => $now_str
            ]);

        } elseif ($geschaeftsstelle_id == 1 || $geschaeftsstelle_id == 2){

            $rs = $this->sDao->getRequestTimePeriodByRegion12([
                'geschaeftsstelle_id1' => 1,
                'geschaeftsstelle_id2' => 2,
                'beginpoint' => $begin_str,
                'endepoint' => $now_str
            ]);
            
        } else {

            $rs = $this->sDao->getRequestTimePeriodByRegion([
                'geschaeftsstelle_id' => $geschaeftsstelle_id,
                'beginpoint' => $begin_str,
                'endepoint' => $now_str
            ]);
            
        }

        $temp['req_anzahl'] = (int)$rs['req_anzahl'];

        return $temp;
    }

    public function objectRequest12Period(int $geschaeftsstelle_id) {

        $now = new \DateTime();
        $begin = new \DateTime();
        $begin->modify('-4 week');

        $result = [];

        for ($i = 0; $i < 12; $i++) {

            if($i>0){
                $now->modify('-4 week');
                $begin->modify('-4 week');
            }

            $temp = $this->objectRequestPeriod($geschaeftsstelle_id, $begin, $now);
            $result[] = $temp;
             
        }

        return $result;
    }

    public function getRegionName(int $geschaeftsstelle_id) {
        $geschaeftsstelle_name = '';
        if($geschaeftsstelle_id==1||$geschaeftsstelle_id==2){
            $geschaeftsstelle_name = 'Ivd-Süd';
        } elseif($geschaeftsstelle_id==3) {
            $geschaeftsstelle_name = 'Ivd-Nord';
        } elseif($geschaeftsstelle_id==4) {
            $geschaeftsstelle_name = 'Ivd-Mitte-Ost';
        } elseif($geschaeftsstelle_id==5) {
            $geschaeftsstelle_name = 'Ivd-Berlin-Brandenburg';
        } elseif($geschaeftsstelle_id==6){
            $geschaeftsstelle_name = '';
        } elseif($geschaeftsstelle_id==7){
            $geschaeftsstelle_name = 'Ivd-West';
        } elseif($geschaeftsstelle_id==8){
            $geschaeftsstelle_name = 'Ivd-Mitte';
        }
        return $geschaeftsstelle_name;
    }

    public function objectRequestLineData(int $geschaeftsstelle_id) {
        $sum = [];
        // $total = $this->objectRequest12Period(6);
        $total = $this->tempFileHandler->getTempoContent();

        if($geschaeftsstelle_id == 6){
            for ($i = 0; $i < 12; $i++) {
                $temp = [
                    'day' => $total[$i]['now'],
                    'gesamt' => $total[$i]['req_anzahl']
                ];
                $sum[] = $temp;
            }

        } else {
            $region = $this->objectRequest12Period($geschaeftsstelle_id);
            $geschaeftsstelle_name = $this->getRegionName($geschaeftsstelle_id);

            for ($i = 0; $i < 12; $i++) {
                $temp = [
                    'day' => $total[$i]['now'],
                    'gesamt' => $total[$i]['req_anzahl'],
                    $geschaeftsstelle_name => $region[$i]['req_anzahl']
                ];
                $sum[] = $temp;
            }
        }
        
        return $sum;
    }

    public function getLineDataData(int $geschaeftsstelle_id) {
        // get total array
        $total = $this->objectRequest12Period(6);
        // store in tempore
        $this->tempFileHandler->setTempoContent($total);  

        $lineData = $this->objectRequestLineData($geschaeftsstelle_id);
        return $lineData;
    }

    public function getLineDataData2(int $geschaeftsstelle_id) {
        // get total array
        // $total = $this->objectRequest12Period(6);
        // store in tempore
        // $this->tempFileHandler->setTempoContent($total);  

        $lineData = $this->objectRequestLineData($geschaeftsstelle_id);
        return $lineData;
    }

}