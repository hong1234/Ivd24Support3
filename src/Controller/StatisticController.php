<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\HttpFoundation\Request;
// use App\Repository\ProductRepository;

use App\Dao\MaklerDao;
use App\Dao\StatisticDao;
use App\Dao\ObjectDao;

/**
 *
 * @Route(path="/statistic")
 */
class StatisticController extends AbstractController
{
    /**
     * @Route("/dashboard", name="statistic_dashboard")
     */
    public function Dashboard(MaklerDao $mDao, StatisticDao $sDao, ObjectDao $oDao)
    {
        //$this->getUser()->getEmail();
        //$roles = $this->getUser()->getRoles();
        //var_dump($this->getUser()->getUserid());exit;

        $user_id = $this->getUser()->getUserid();

        $ac = $sDao->getUserGeschaeftsstelle([
            'user_id' => $user_id
        ]);

        $geschaeftsstelle_id   = $ac['geschaeftsstellen_id'];
        $geschaeftsstelle_name = $ac['name'];

        if($geschaeftsstelle_name=='ivd24immobilien AG'){  // $geschaeftsstelle_id =6;
            $stmt = $sDao->getActivMaklerProRegion();
        } else {
            $stmt = $sDao->getActivMaklerOnRegion([
                'geschaeftsstelle_id' => $geschaeftsstelle_id
            ]);
        }

        $rows1 = array();
        while($row = $stmt->fetch()) {
            $rows1[] = $row;
        }

        if($geschaeftsstelle_name=='ivd24immobilien AG'){
            $stmt = $sDao->getActivMaklerHaveObjectProRegion();
        } else {
            $stmt = $sDao->getActivMaklerHaveObjectOnRegion([
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

        //makler table-----------

        // $stmt = $mDao->getAllMakler();
        
        // $rows = array();
        // while ($row = $stmt->fetch()) {
        //     $row2 = array();
        //     $row2[] = $row['userId'];
        //     $row2[] = $row['vorname'].' '.$row['name'];
        //     $row2[] = $row['firma'];
        //     $row2[] = $row['maklerEmail'];
        //     $row2[] = date("Y-m-d", (int)$row['registrierungsdatum']);
        //     $row2[] = date("Y-m-d", (int)$row['lastlogin']);
        
        //     //$str1 = "<a href='/admin/makler/".$row['userId']."/edit'>Daten bearbeiten</a><br>";
        //     $str1 = "<a href=".$this->generateUrl('makler_edit', array('uid' => $row['userId'])).">Daten bearbeiten</><br>";
        //     $str2 = "<a href=".$this->generateUrl('makler_ftp_edit', array('uid' => $row['userId'])).">FTP-Passwort bearbeiten</a><br>";
        //     $str3 = "<a href=".$this->generateUrl('makler_pw_edit', array('uid' => $row['userId'])).">Passwort bearbeiten</a><br>";
        //     $str4 = "";
        //     if($row['gesperrt']==1){
        //         $str4 = "<a href=".$this->generateUrl('makler_lock_unlock', array('uid' => $row['userId'], 'gesperrt' => 0)).">Account entsperren</a><br>";
        //     } else {
        //         $str4 = "<a href=".$this->generateUrl('makler_lock_unlock', array('uid' => $row['userId'], 'gesperrt' => 1)).">Account sperren</a><br>";
        //     }
        //     $row2[] = $str1.$str2.$str3.$str4;

        //     $rows[] = $row2;
        // }

        $total = $oDao->getObjectTotal()['Anzah_Gesamtl_Objekte'];
        $activ = $oDao->getObjectActiv()['Anzahl_freigegeben_Objekte'];
        $inact = $oDao->getObjectInActiv()['Anzahl_nicht_freigegeben_Objekte'];

        $rows = array(
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
        
        return $this->render('statistic/dashboard3.html.twig', [
            'dataSet' => $rows,
            'rowsB'   => $rowsB,
            'CssArray' => ["bg-aqua", "bg-green", "bg-yellow", "bg-red", "bg-blue"]
        ]);
    }

    /**
     * @Route("/bayern", name="statistic_bayern")
     */
    public function bayernPage()
    {
        // return $this->json([
        //     'message' => 'Welcome to your new controller!',
        //     'path' => 'src/Controller/DefaultController.php',
        // ]);

        return $this->render('statistic/bayern.html.twig', [
            
        ]);
    }

}