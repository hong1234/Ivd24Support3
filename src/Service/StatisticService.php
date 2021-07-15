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

    public function getBoxsData(int $user_id) {

        $ac = $this->sDao->getUserGeschaeftsstelle([
            'user_id' => $user_id
        ]);

        $geschaeftsstelle_id   = $ac['geschaeftsstellen_id'];
        $geschaeftsstelle_name = $ac['name'];

        if($geschaeftsstelle_name=='ivd24immobilien AG'){  // $geschaeftsstelle_id =6;
            $stmt = $this->sDao->getActivMaklerProRegion();
        } else {
            $stmt = $this->sDao->getActivMaklerOnRegion([
                'geschaeftsstelle_id' => $geschaeftsstelle_id
            ]);
        }

        $rows1 = array();
        while($row = $stmt->fetch()) {
            $rows1[] = $row;
        }

        if($geschaeftsstelle_name=='ivd24immobilien AG'){
            $stmt = $this->sDao->getActivMaklerHaveObjectProRegion();
        } else {
            $stmt = $this->sDao->getActivMaklerHaveObjectOnRegion([
                'geschaeftsstelle_id' => $geschaeftsstelle_id
            ]);
        }

        $rows2 = array();
        while($row = $stmt->fetch()) {
            $rows2[] = $row;
        }

        // result -----
        $i = 0;
        $rowsB = array();
        while ($i < count($rows1)) {
            $tmp = array();
            $a = $rows1[$i];
            $b = $rows2[$i];

            $tmp['name'] = $a['name'];
            $tmp['count_makler_on_regional_office'] = $a['count_makler_on_regional_office'];
            //$tmp['count_makler_with_aktive_objectdata'] = $b['count_makler_with_aktive_objectdata'];
            $tmp['percent'] = round($b['count_makler_with_aktive_objectdata']/$a['count_makler_on_regional_office'], 2)*100;

            $rowsB[] = $tmp;
      
            $i++;
        }
        
        return $rowsB;
    }

    public function getDonutData(int $user_id) {

        $total = $this->oDao->getObjectTotal()['Anzah_Gesamtl_Objekte'];
        $activ = $this->oDao->getObjectActiv()['Anzahl_freigegeben_Objekte'];
        $inact = $this->oDao->getObjectInActiv()['Anzahl_nicht_freigegeben_Objekte'];

        $donutData = array(
            [
                'label' => 'Gesamtl Objekte',
                'value' => $total
            ],
            [
                'label' => 'freigegeben Objekte',
                'value' => $activ
            ],
            [
                'label' => 'nicht freigegeben Objekte',
                'value' => $inact
            ]
        );

        return $donutData;
    }

    public function getAreaData(int $user_id) {
        $areaData = array(
            [
                'day' =>'2020-02-01',
                'gesamt' =>8700,
                'frei' =>5700,
                'nfrei' =>3000
            ],
            [
                'day' =>'2020-03-01',
                'gesamt' =>2700,
                'frei' =>1700,
                'nfrei' =>1000
            ],
            [
                'day' =>'2020-04-01',
                'gesamt' =>3000,
                'frei' =>2000,
                'nfrei' =>900
            ],
            [
                'day' =>'2020-05-01',
                'gesamt' =>2666,
                'frei' =>1666,
                'nfrei' =>1000
            ],
            [
                'day' =>'2020-06-01',
                'gesamt' =>2778,
                'frei' =>2294,
                'nfrei' =>500
            ],
            [
                'day' =>'2020-07-01',
                'gesamt' =>4912,
                'frei' =>1969,
                'nfrei' =>2000
            ],
            [
                'day' =>'2020-08-01',
                'gesamt' =>3767,
                'frei' =>3597,
                'nfrei' =>100
            ],
            [
                'day' =>'2020-09-01',
                'gesamt' =>6810,
                'frei' =>1914,
                'nfrei' =>3000
            ],
            [
                'day' =>'2020-10-01',
                'gesamt' =>5670,
                'frei' =>4293,
                'nfrei' =>1000
            ],
            [
                'day' =>'2020-11-01',
                'gesamt' =>4820,
                'frei' =>3795,
                'nfrei' =>1100
            ],
            [
                'day' =>'2020-12-01',
                'gesamt' =>15073,
                'frei' =>5967,
                'nfrei' =>9000
            ],
            [
                'day' =>'2021-01-01',
                'gesamt' =>10687,
                'frei' =>4460,
                'nfrei' =>5000
            ],
            [
                'day' =>'2021-02-01',
                'gesamt' =>8432,
                'frei' =>5713,
                'nfrei' =>3000
            ]
        );
        return $areaData;
    }

    public function getLineDataData(int $user_id) {
        $lineData = array(
            [
                'day' =>'2020-02-01',
                'gesamt' =>8666,
                'ivdSud' =>6600
            ],
            [
                'day' =>'2020-03-01',
                'gesamt' =>5666,
                'ivdSud' =>2800
            ],
            [
                'day' =>'2020-04-01',
                'gesamt' =>3948,
                'ivdSud' =>2000
            ],
            [
                'day' =>'2020-05-01',
                'gesamt' =>2666,
                'ivdSud' =>1333
            ],
            [
                'day' =>'2020-06-01',
                'gesamt' =>2778,
                'ivdSud' =>1433
            ],
            [
                'day' =>'2020-07-01',
                'gesamt' =>4912,
                'ivdSud' =>2333
            ],
            [
                'day' =>'2020-08-01',
                'gesamt' =>3767,
                'ivdSud' =>2633
            ],
            [
                'day' =>'2020-09-01',
                'gesamt' =>6810,
                'ivdSud' =>3333
            ],
            [
                'day' =>'2020-10-01',
                'gesamt' =>5670,
                'ivdSud' =>4333
            ],
            [
                'day' =>'2020-11-01',
                'gesamt' =>4820,
                'ivdSud' =>2410
            ],
            [
                'day' =>'2020-12-01',
                'gesamt' =>15073,
                'ivdSud' =>8000
            ],
            [
                'day' =>'2021-01-01',
                'gesamt' =>10687,
                'ivdSud' =>5000
            ],
            [
                'day' =>'2021-02-01',
                'gesamt' =>8432,
                'ivdSud' =>4216
            ]
        );
        return $lineData;
    }


}