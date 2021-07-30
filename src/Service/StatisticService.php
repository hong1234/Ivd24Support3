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

            $reg1 = $this->sDao->getMaklerOnRegion1And2();
            
            $row = [
                'name' => 'IVD Süd',
                'geschaeftsstelle_id' => $geschaeftsstelle_id,
                'count_makler_on_regional_office' => $reg1['count_makler_on_regional_office']
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

            $reg1 = $this->sDao->getMaklerHaveObjectOnRegion1And2();
           
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

            $total = $this->oDao->getObjectTotalByRegion1And2()['Anzah_Gesamtl_Objekte'];
            $activ = $this->oDao->getObjectActivByRegion1And2()['Anzahl_freigegeben_Objekte'];
            $inact = $this->oDao->getObjectInActivByRegion1And2()['Anzahl_nicht_freigegeben_Objekte'];

        } else {

            $pars = ['geschaeftsstelle_id' => $geschaeftsstelle_id];
            $total = $this->oDao->getObjectTotalByRegion($pars)['Anzah_Gesamtl_Objekte'];
            $activ = $this->oDao->getObjectActivByRegion($pars)['Anzahl_freigegeben_Objekte'];
            $inact = $this->oDao->getObjectInActivByRegion($pars)['Anzahl_nicht_freigegeben_Objekte'];
        }

        return ['total'=>$total, 'activ'=>$activ, 'inact'=>$inact];
    }

    public function statisticObjectDonut(int $geschaeftsstelle_id) {

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

    public function statisticObjectArea(int $geschaeftsstelle_id) {

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

    public function objectRequestLast12Months() {
        $arr = $this->sDao->getRequestLast12Months();
        $begin = count($arr) - 13;
        $subArray = array_slice($arr, $begin, 12);
        return $subArray;
    }

    public function objectRequestLast12MonthsByRegion(int $geschaeftsstelle_id) {
        $arr = $this->sDao->getRequestLast12MonthsByRegion([
            'geschaeftsstelle_id' => $geschaeftsstelle_id
        ]);
        $begin = count($arr) - 13;
        $subArray = array_slice($arr, $begin, 12);
        return $subArray;
    }

    public function objectRequestLast12MonthsByRegion1And2() {
        $arr = $this->sDao->getRequestLast12MonthsByRegion1And2();
        $begin = count($arr) - 13;
        $subArray = array_slice($arr, $begin, 12);
        return $subArray;
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

        $total = $this->tempFileHandler->getTempoContent();

        if($geschaeftsstelle_id == 6){
            foreach($total as $item) {
                $temp = [
                    'day' => $item['time_span'],
                    'gesamt' => $item['req_az']
                ];
                $sum[] = $temp;
            }
        } else {

            if ($geschaeftsstelle_id == 1 || $geschaeftsstelle_id == 2){
                $region = $this->objectRequestLast12MonthsByRegion1And2();
            } else {
                $region = $this->objectRequestLast12MonthsByRegion($geschaeftsstelle_id);
            }

            $geschaeftsstelle_name = $this->getRegionName($geschaeftsstelle_id);

            for ($i = 0; $i < 12; $i++) {
                $temp = [
                    'day' => $total[$i]['time_span'],
                    'gesamt' => $total[$i]['req_az'],
                    $geschaeftsstelle_name => $region[$i]['req_az']
                ];
                $sum[] = $temp;
            }

        }
        
        return $sum;
    }

    public function statisticRequestLine(int $geschaeftsstelle_id) {
        // get total array
        $total = $this->objectRequestLast12Months();
        // store in tempore
        $this->tempFileHandler->setTempoContent($total);  

        $lineData = $this->objectRequestLineData($geschaeftsstelle_id);
        return $lineData;
    }

    public function statisticRequestLine2(int $geschaeftsstelle_id) {
        $lineData = $this->objectRequestLineData($geschaeftsstelle_id);
        return $lineData;
    }

}