<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\HttpFoundation\Request;

use App\Service\StockService;

use App\Dao\ObjectDao;

/**
 *
 * @Route(path="/support")
 */
class StockController extends AbstractController
{
    private $stockService;
   
    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
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
            // 'AnzahlAktienGesamt' => $AnzahlAktienGesamt,
            // 'AnzahlAktienAG' => $AnzahlAktienAG,
            // 'AnzahlAktienOhneZuweisungZuStakeholder' => $AnzahlAktienOhneZuweisungZuStakeholder,
            // 'AnzahlAktienMitZuweisungZuStakeholder' => $AnzahlAktienMitZuweisungZuStakeholder,
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

}