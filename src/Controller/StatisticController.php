<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\HttpFoundation\Request;
// use App\Repository\ProductRepository;

// use App\Dao\StatisticDao;

use App\Service\StatisticService;

/**
 *
 * @Route(path="/statistic")
 */
class StatisticController extends AbstractController
{
    private $sService;

    public function __construct(StatisticService $sService)
    {
        $this->sService = $sService;
    }

    /**
     * @Route("/dashboard", name="statistic_dashboard")
     */
    public function Dashboard()
    {
        //$this->getUser()->getEmail();
        //$roles = $this->getUser()->getRoles();

        $user_id = $this->getUser()->getUserid();
        $boxs = $this->sService->getBoxsData($user_id);

        //makler table-----------
        // $rows = $mService->MaklerList();

        $donutData = $this->sService->getDonutData($user_id);
        $areaData = $this->sService->getAreaData($user_id);
        $lineData = $this->sService->getLineDataData($user_id);
        
        return $this->render('statistic/dashboard.html.twig', [
            'lineData'  => $lineData,
            'areaData'  => $areaData,
            'donutData' => $donutData,
            'rowsB'     => $boxs,
            'CssArray'  => ["bg-aqua", "bg-green", "bg-yellow", "bg-red", "bg-blue"]
        ]);
    }

}