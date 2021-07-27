<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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
     * @Route("/stock/notshareholders", name="stock_notshareholders")
     */
    public function notShareholderList()
    {
        $rows = $this->stockService->notShareholderList();
        return $this->render('stock/notshareholders.html.twig', [
            'dataSet' => $rows
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
     * @Route("/stock/notverified", name="stock_notverified")
     */
    public function notVerifiedList()
    {
        $rows = $this->stockService->notVerifiedList();
        return $this->render('stock/notverifiedlist.html.twig', [
            'dataSet' => $rows
        ]);
    }

    /**
     * @Route("/stock/verify/{userid}", name="stock_verify", requirements={"userid"="\d+"})
     */
    public function verifyUserAktien(int $userid, Request $request)
    {
        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            $this->stockService->verifyUserAktien($userid);
            return $this->redirectToRoute('stock_notverified', [
                //'paramName' => 'value'
            ]);
        }

        if ($request->isMethod('GET')) {
            $makler = $this->stockService->maklerData($userid);
            $aktien = $this->stockService->maklerAktien($userid);
            $docs   = $this->stockService->aktienDoc($userid);
            // var_dump($aktien); exit;
        }
        return $this->render('stock/verify.html.twig', [
            'user_id' => $userid,
            'makler' => $makler,
            'aktien' => $aktien,
            'docs' => $docs
        ]);
    }

}