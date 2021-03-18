<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\HttpFoundation\Request;
// use App\Repository\ProductRepository;

use App\Dao\MaklerDao;

/**
 *
 * @Route(path="/statistic")
 */
class StatisticController extends AbstractController
{
    /**
     * @Route("/dashboard", name="statistic_dashboard")
     */
    public function Dashboard(MaklerDao $mDao)
    {
        $stmt = $mDao->getActivMaklerProRegion();
        $rows1 = array();
        while($row = $stmt->fetchAssociative()) {
            $rows1[] = $row;
        }

        $stmt = $mDao->getActivMaklerHaveObjectProRegion();
        $rows2 = array();
        while($row = $stmt->fetchAssociative()) {
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

        $stmt = $mDao->getAllMakler();
        $rows = array();
        while($row = $stmt->fetchAssociative()) {
            $row2 = array();
            $row2[] = $row['userId'];
            $row2[] = $row['vorname'].' '.$row['name'];
            $row2[] = $row['firma'];
            $row2[] = $row['maklerEmail'];
            $row2[] = date("Y-m-d", (int)$row['registrierungsdatum']);
            $row2[] = date("Y-m-d", (int)$row['lastlogin']);

            $str1 = "<a href='/makler/".$row['userId']."/edit'>Daten bearbeiten</a><br>";
            $str2 = "<a href='/makler/".$row['userId']."/ftpedit'>FTP-Passwort bearbeiten</a><br>";
            $str3 = "<a href='/makler/".$row['userId']."/pwedit'>Passwort bearbeiten</a><br>";
            $str4 = "";
            if($row['gesperrt']==1){
                $str4 = "<a href='/makler/".$row['userId']."/lock/0'>Account entsperren</a><br><br>";
            } else {
                $str4 = "<a href='/makler/".$row['userId']."/lock/1'>Account sperren</a><br><br>";
            }
            $row2[] = $str1.$str2.$str3.$str4;

            $rows[] = $row2;
        }

        //$dataSet = json_encode($rows);
        
        return $this->render('statistic/dashboard.html.twig', [
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