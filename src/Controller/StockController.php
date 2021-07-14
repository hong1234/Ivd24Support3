<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\HttpFoundation\Request;

use App\Service\StockService;

use App\Dao\StockDao;

/**
 *
 * @Route(path="/support")
 */
class StockController extends AbstractController
{
    private $stockService;
    private $stockDao;
   
    public function __construct(StockDao $stockDao, StockService $stockService)
    {
        $this->stockService = $stockService;
        $this->stockDao = $stockDao;
    }

    /**
     * @Route("/stock/statistic", name="stock_statistic")
     */
    public function stockStatistic()
    {
        $AnzahlAktienGesamt = $this->stockService->getStockNumber();
        $AnzahlAktienAG = $this->stockService->getStockNumberAG();
        $AnzahlAktienOhneZuweisungZuStakeholder = $this->stockService->getStockNumberWithOutStakeholder();
        $AnzahlAktienMitZuweisungZuStakeholder = $this->stockService->getStockNumberWithStakeholder();

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

        return $this->render('stock/statistic.html.twig', [
            'donutData' => $donutData
        ]);
    }

    /**
     * @Route("/stock/shareholders", name="stock_shareholders")
     */
    public function shareholderList()
    {
        $rows = $this->stockService->shareholderList();
        return $this->render('stock/shareholders.html.twig', [
            'dataSet' => $rows
        ]);
    }

    /**
     * @Route("/stock/generalmeeting", name="stock_generalmeeting")
     */
    public function generalMeeting()
    {
        // $rows = $this->stockService->shareholderList();
        return $this->render('stock/generalmeeting.html.twig', [
            'message' => "Opps, derzeit eine Baustelle !"
        ]);
    }

    /**
     * @Route("/stock/test", name="stock_test")
     */
    public function stockTest()
    {
        // $rows = $this->stockDao->getStakeholders();

        // $rows = $this->stockDao->getMembersOfStakeholder([
        //     'user_id_stakeholder' => 112049 // 112138
        // ]);

        // $rows = $this->stockDao->getAktienAnzahlOfUser([
        //     'user_id' => 112049
        // ]);

        // $rows = $this->stockDao->getAktienAnzahlOfStakeholder([
        //     'user_id_stakeholder' => 112049 // 112138
        // ]);

        $rows = $this->stockDao->getStakeholderStruktur();

//         array (size=2)
//   0 => 
//     array (size=4)
//       'user_id_stakeholder' => string '112049' (length=6)
//       'stakeholder' => string 'Test Stakeholder IVDWest' (length=24)
//       'aktien_az' => string '1' (length=1)
//       'aktien_az_stakeholder' => string '1' (length=1)
//   1 => 
//     array (size=4)
//       'user_id_stakeholder' => string '112138' (length=6)
//       'stakeholder' => string 'Test Stakeholder IVDSüd' (length=24)
//       'aktien_az' => string '4' (length=1)
//       'aktien_az_stakeholder' => string '1' (length=1)

        // $rows = $this->stockDao->getStockNumberWithOutStakeholder();

        var_dump($rows); exit;

        return $this->render('stock/test.html.twig', [
            'rows' => $rows
        ]);
    }

}