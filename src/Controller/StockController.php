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
        $shareholderStruktur = $this->stockService->getShareholderStruktur();
        $stakeholderStruktur = $this->stockService->getStakeholderStruktur();

        return $this->render('stock/statistic.html.twig', [
            'donutData' => $shareholderStruktur,
            'stakeholderStruktur' => $stakeholderStruktur
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
        $rows = $this->stockService->getStakeholderStruktur();
        var_dump($rows); exit;
        
        return $this->render('stock/test.html.twig', [
            'rows' => $rows
        ]);
    }

}