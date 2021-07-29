<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use App\Service\StatisticService;
use App\Dao\StatisticDao;

/**
 *
 * @Route(path="/statistic")
 */
class StatisticController extends AbstractController
{
    private $sService;
    private $sDao;

    public function __construct(StatisticDao $sDao, StatisticService $sService)
    {
        $this->sService = $sService;
        $this->sDao = $sDao;
    }

    /**
     * @Route("/dashboard/region/{regid}", name="statistic_dashboard_region", requirements={"regid"="\d+"})
     */
    public function DashboardRegion(int $regid)
    {
        $geschaeftsstelle_id = $regid;
        
        $boxs      = $this->sService->statisticMakler($geschaeftsstelle_id);
        $donutData = $this->sService->getDonutData($geschaeftsstelle_id);
        $areaData  = $this->sService->getAreaData($geschaeftsstelle_id);
        $lineData  = $this->sService->getLineDataData2($geschaeftsstelle_id);

        $geschaeftsstelle_name = $this->sService->getRegionName($geschaeftsstelle_id);
        
        return $this->render('statistic/dashboard.html.twig', [
            'lineData'  => $lineData,
            'areaData'  => $areaData,
            'donutData' => $donutData,
            'rows' => $boxs,
            'geschaeftsstelle_id' => $geschaeftsstelle_id,
            'geschaeftsstelle_name' => $geschaeftsstelle_name,
            'CssArray'  => ["bg-aqua", "bg-green", "bg-yellow", "bg-red", "bg-blue"]
        ]);

    }

    /**
     * @Route("/dashboard", name="statistic_dashboard")
     */
    public function Dashboard()
    {
        // $this->getUser()->getEmail();
        // $roles = $this->getUser()->getRoles();

        //makler table-----------
        // $rows = $mService->MaklerList();

        $user_id = $this->getUser()->getUserid();
        $geschaeftsstelle_id = $this->sService->geschaeftsstelleId($user_id);

        $boxs      = $this->sService->statisticMakler($geschaeftsstelle_id);
        $donutData = $this->sService->getDonutData($geschaeftsstelle_id);
        $areaData  = $this->sService->getAreaData($geschaeftsstelle_id);
        $lineData  = $this->sService->getLineDataData($geschaeftsstelle_id);

        $geschaeftsstelle_name = $this->sService->getRegionName($geschaeftsstelle_id);
        
        return $this->render('statistic/dashboard.html.twig', [
            'lineData'  => $lineData,
            'areaData'  => $areaData,
            'donutData' => $donutData,
            'rows'     => $boxs,
            'geschaeftsstelle_id' => $geschaeftsstelle_id,
            'geschaeftsstelle_name' => $geschaeftsstelle_name,
            'CssArray'  => ["bg-aqua", "bg-green", "bg-yellow", "bg-red", "bg-blue"]
        ]);
    }

    /**
     * @Route("/csv/activmakler/{gsid}", name="statistic_csv_activmakler", requirements={"gsid"="\d+"})
     */
    public function csvActivMakler(int $gsid)
    {
        $geschaeftsstelle_id = $gsid;

        if ($geschaeftsstelle_id == 1 || $geschaeftsstelle_id == 2) {
            $rows1 = $this->sDao->getActivMakler([
                'geschaeftsstelle_id' => 1
            ]);
            $rows2 = $this->sDao->getActivMakler([
                'geschaeftsstelle_id' => 2
            ]);
            $rows = array_merge($rows1, $rows2);
        } else {
            $rows = $this->sDao->getActivMakler([
                'geschaeftsstelle_id' => $geschaeftsstelle_id
            ]);
        }
        
        $response = $this->render('statistic/list.csv.twig', [
            'rows' => $rows
        ]);

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $d = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            "ActivMaklerList-".date('Y-m-d').".csv"
        );
        $response->headers->set('Content-Disposition', $d);
        $response->sendHeaders();
        print "\xEF\xBB\xBF"; // UTF-8 BOM - hack for correct encoding in excel
        
        return $response;
    }

    /**
     * @Route("/csv/inactivmakler/{gsid}", name="statistic_csv_inactivmakler", requirements={"gsid"="\d+"})
     */
    public function csvInActivMakler(int $gsid)
    {
        $geschaeftsstelle_id = $gsid;

        if ($geschaeftsstelle_id == 1 || $geschaeftsstelle_id == 2) {
            $rows1 = $this->sDao->getInActivMakler([
                'geschaeftsstelle_id' => 1
            ]);
            $rows2 = $this->sDao->getInActivMakler([
                'geschaeftsstelle_id' => 2
            ]);
            $rows = array_merge($rows1, $rows2);
        } else {
            $rows = $this->sDao->getInActivMakler([
                'geschaeftsstelle_id' => $geschaeftsstelle_id
            ]);
        }
        
        $response = $this->render('statistic/list.csv.twig', [
            'rows' => $rows
        ]);

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $d = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            "InActivMaklerList-".date('Y-m-d').".csv"
        );
        $response->headers->set('Content-Disposition', $d);
        $response->sendHeaders();
        print "\xEF\xBB\xBF"; // UTF-8 BOM - hack for correct encoding in excel
        
        return $response;
    }

}