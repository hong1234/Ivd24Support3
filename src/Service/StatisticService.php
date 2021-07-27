<?php
namespace App\Service;

use App\Dao\StatisticDao;
use App\Dao\ObjectDao;

class StatisticService
{
    private $sDao;
    private $oDao;
    
    public function __construct(StatisticDao $sDao, ObjectDao $oDao) {
        $this->sDao = $sDao;
        $this->oDao = $oDao; 
    }

    public function geschaeftsstelleId(int $user_id) {
        $ac = $this->sDao->getUserGeschaeftsstelle([
            'user_id' => $user_id
        ]);

        $geschaeftsstelle_id = (int)$ac['geschaeftsstellen_id'];
        // $geschaeftsstelle_name = $ac['name'];
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

            $reg1 = $this->sDao->getMaklerHaveObjectOnRegion(['geschaeftsstelle_id' => 1]);
            $reg2 = $this->sDao->getMaklerHaveObjectOnRegion(['geschaeftsstelle_id' => 2]);

            $row = [
                'name' => 'IVD Süd',
                'geschaeftsstelle_id' => $geschaeftsstelle_id,
                'count_makler_with_aktive_objectdata' => $reg1['count_makler_with_aktive_objectdata'] + $reg2['count_makler_with_aktive_objectdata']
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

            $total1 = $this->oDao->getObjectTotal2(['geschaeftsstelle_id' => 1])['Anzah_Gesamtl_Objekte'];
            $activ1 = $this->oDao->getObjectActiv2(['geschaeftsstelle_id' => 1])['Anzahl_freigegeben_Objekte'];
            $inact1 = $this->oDao->getObjectInActiv2(['geschaeftsstelle_id' => 1])['Anzahl_nicht_freigegeben_Objekte'];

            $total2 = $this->oDao->getObjectTotal2(['geschaeftsstelle_id' => 2])['Anzah_Gesamtl_Objekte'];
            $activ2 = $this->oDao->getObjectActiv2(['geschaeftsstelle_id' => 2])['Anzahl_freigegeben_Objekte'];
            $inact2 = $this->oDao->getObjectInActiv2(['geschaeftsstelle_id' => 2])['Anzahl_nicht_freigegeben_Objekte'];

            $total = $total1 + $total2;
            $activ = $activ1 + $activ2;
            $inact = $inact1 + $inact2;

        } else {
            
            $total = $this->oDao->getObjectTotal2(['geschaeftsstelle_id' => $geschaeftsstelle_id])['Anzah_Gesamtl_Objekte'];
            $activ = $this->oDao->getObjectActiv2(['geschaeftsstelle_id' => $geschaeftsstelle_id])['Anzahl_freigegeben_Objekte'];
            $inact = $this->oDao->getObjectInActiv2(['geschaeftsstelle_id' => $geschaeftsstelle_id])['Anzahl_nicht_freigegeben_Objekte'];
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
        
        $areaData = array(
            [
                'day' =>'2020-06-01',
                'gesamt' => $rs['total'],
                'frei' => $rs['activ'],
                'nfrei' => $rs['inact']
            ],
            [
                'day' =>'2020-07-01',
                'gesamt' => $rs['total'],
                'frei' => $rs['activ'],
                'nfrei' => $rs['inact']
            ],
            [
                'day' =>'2020-08-01',
                'gesamt' => $rs['total'],
                'frei' => $rs['activ'],
                'nfrei' => $rs['inact']
            ],
            [
                'day' =>'2020-09-01',
                'gesamt' => $rs['total'],
                'frei' => $rs['activ'],
                'nfrei' => $rs['inact']
            ],
            [
                'day' =>'2020-10-01',
                'gesamt' => $rs['total'],
                'frei' => $rs['activ'],
                'nfrei' => $rs['inact']
            ],
            [
                'day' =>'2020-11-01',
                'gesamt' => $rs['total'],
                'frei' => $rs['activ'],
                'nfrei' => $rs['inact']
            ],
            [
                'day' =>'2020-12-01',
                'gesamt' => $rs['total'],
                'frei' => $rs['activ'],
                'nfrei' => $rs['inact']
            ],
            [
                'day' =>'2021-01-01',
                'gesamt' => $rs['total'],
                'frei' => $rs['activ'],
                'nfrei' => $rs['inact']
            ],
            [
                'day' =>'2021-02-01',
                'gesamt' => $rs['total'],
                'frei' => $rs['activ'],
                'nfrei' => $rs['inact']
            ],
            [
                'day' =>'2021-03-01',
                'gesamt' => $rs['total'],
                'frei' => $rs['activ'],
                'nfrei' => $rs['inact']
            ],
            [
                'day' =>'2021-04-01',
                'gesamt' => $rs['total'],
                'frei' => $rs['activ'],
                'nfrei' => $rs['inact']
            ],
            [
                'day' =>'2021-05-01',
                'gesamt' => $rs['total'],
                'frei' => $rs['activ'],
                'nfrei' => $rs['inact']
            ],
            [
                'day' =>'2021-06-01',
                'gesamt' => $rs['total'],
                'frei' => $rs['activ'],
                'nfrei' => $rs['inact']
            ]
        );

        return $areaData;
    }

    public function statisticObjectRequest(int $geschaeftsstelle_id) {

        $result = [];
        $now = new \DateTime();
        $begin = new \DateTime();
        $begin->modify('-4 week');

        for ($c = 0; $c < 12; $c++) {

            if($c>0){
                $now->modify('-4 week');
                $begin->modify('-4 week');
            }

            $rs = [
                'now' => $now->format('Y-m-d'),
                'begin' => $begin->format('Y-m-d')
            ];
    
            $now_str = $now->format('Y-m-d H:i:s');
            $begin_str = $begin->format('Y-m-d H:i:s');
    
            if($geschaeftsstelle_id == 6) {
    
                $rs1 = $this->sDao->getRequestTimePeriod([
                    'beginpoint' => $begin_str,
                    'endepoint' => $now_str
                ]);
                $rs['req_anzahl'] = (int)$rs1['req_anzahl'];
    
            } elseif ($geschaeftsstelle_id == 1 || $geschaeftsstelle_id == 2){
                
                $rs1 = $this->sDao->getRequestTimePeriodByRegion([
                    'geschaeftsstelle_id' => 1,
                    'beginpoint' => $begin_str,
                    'endepoint' => $now_str
                ]);
                $rs2 = $this->sDao->getRequestTimePeriodByRegion([
                    'geschaeftsstelle_id' => 2,
                    'beginpoint' => $begin_str,
                    'endepoint' => $now_str
                ]);
                $rs['req_anzahl'] = (int)$rs1['req_anzahl'] + (int)$rs2['req_anzahl'];
    
            } else {
    
                $rs1 = $this->sDao->getRequestTimePeriodByRegion([
                    'geschaeftsstelle_id' => $geschaeftsstelle_id,
                    'beginpoint' => $begin_str,
                    'endepoint' => $now_str
                ]);
                $rs['req_anzahl'] = (int)$rs1['req_anzahl'];
    
            }

            $result[] = $rs;
             
        }

        return $result;
    }

    public function getRegionName(int $geschaeftsstelle_id) {
        $geschaeftsstelle_name = '';
        if($geschaeftsstelle_id==1||$geschaeftsstelle_id==2){
            $geschaeftsstelle_name = 'ivd-sued';
        } elseif($geschaeftsstelle_id==3) {
            $geschaeftsstelle_name = 'ivd-nord';
        } elseif($geschaeftsstelle_id==4) {
            $geschaeftsstelle_name = 'ivd-mitte-ost';
        } elseif($geschaeftsstelle_id==5) {
            $geschaeftsstelle_name = 'ivd-berlin-brandenburg';
        } elseif($geschaeftsstelle_id==6){
            $geschaeftsstelle_name = '';
        } elseif($geschaeftsstelle_id==7){
            $geschaeftsstelle_name = 'ivd-west';
        } elseif($geschaeftsstelle_id==8){
            $geschaeftsstelle_name = 'ivd-mitte';
        }
        return $geschaeftsstelle_name;
    }

    public function statisticObjectRequestSum(int $geschaeftsstelle_id) {
        $sum = [];
        $total = $this->statisticObjectRequest(6);

        if($geschaeftsstelle_id == 6){

            for ($c = 0; $c < 12; $c++) {
                $temp = [
                    'day' => $total[$c]['now'],
                    'gesamt' => $total[$c]['req_anzahl'],
                    // 'ivdSud' => $region[$c]['req_anzahl']
                ];
                $sum[] = $temp;
            }

        } else {

            $region = $this->statisticObjectRequest($geschaeftsstelle_id);
            $geschaeftsstelle_name = $this->getRegionName($geschaeftsstelle_id);

            for ($c = 0; $c < 12; $c++) {
                $temp = [
                    'day' => $total[$c]['now'],
                    'gesamt' => $total[$c]['req_anzahl'],
                    $geschaeftsstelle_name => $region[$c]['req_anzahl']
                ];
                $sum[] = $temp;
            }
        }
        
        return $sum;
    }

    public function getLineDataData(int $geschaeftsstelle_id) {
        $lineData = $this->statisticObjectRequestSum($geschaeftsstelle_id);
        return $lineData;
    }

}