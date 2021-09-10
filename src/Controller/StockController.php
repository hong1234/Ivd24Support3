<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Service\StockService;

use App\Dao\StockDao;
use App\Dao\UserDao;

/**
 *
 * @Route(path="/support")
 */
class StockController extends AbstractController
{
    private $stockService;
    private $stockDao;
    private $uDao;
   
    public function __construct(UserDao $uDao, StockDao $stockDao, StockService $stockService)
    {
        $this->uDao = $uDao;
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

    //-----------

    /**
     * @Route("/stock/allmeeting", name="stock_allmeeting")
     */
    public function allMeeting()
    {
        $meetings = $this->stockDao->getAllMeetings();

        return $this->render('stock/allmeeting.html.twig', [
            'meetings' => $meetings
        ]);
    }

    /**
     * @Route("/stock/meeting/{hauptversammlung_id}", name="stock_meeting", requirements={"hauptversammlung_id"="\d+"})
     */
    public function meetingInfo(int $hauptversammlung_id, Request $request)
    {
        $meeting = $this->stockDao->getRowInTableByIdentifier2('hauptversammlung', [
            'id' => $hauptversammlung_id
        ]);

        $closed = $meeting->closed;

        $error = '';

        if($closed == 0){

            if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            
                //post parameters
                $safePost = $request->request;
    
                // var_dump( $safePost); exit;
    
                //validation
                // $error = $validator->isValidStatisticUserInput($safePost);
                // $error = 'abc';
                
                if ($error == '') {

                    $this->stockDao->updateProtokoll([
                        'meeting_id' => $hauptversammlung_id,
                        'meeting_protocol' => $safePost->get('protokoll')
                    ]);
    
                    // return $this->redirectToRoute('stock_meetinginvite', [
                    //  'paramName' => 'value'
                    // ]);
                } 
    
                $meeting->protocol_general_meeting = $safePost->get('protokoll');
                
            }

            // if ($request->isMethod('GET')) {
                
            // }

            return $this->render('stock/meeting.html.twig', [
                'hauptversammlung_id' => $hauptversammlung_id,
                'meeting' => $meeting,
                'status' => 'aktiv',
                'error'   => $error
            ]);

        }

        if($closed == 1){

            return $this->render('stock/closedmeeting.html.twig', [
                'meeting' => $meeting,
                'status' => 'geschlossen'
            ]);
        }

    }

    /**
     * @Route("/stock/meetinginvite/{hauptversammlung_id}", name="stock_meetinginvite", requirements={"hauptversammlung_id"="\d+"})
     */
    public function meetingInvite(int $hauptversammlung_id, Request $request)
    {
        $error = '';
        $temps = $this->stockDao->getTemplatesForGeneralMeeting();
        $stories = $this->stockDao->getSendMailTempleteStory();

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            
            //post parameters
            $safePost = $request->request;

            // var_dump( $safePost); exit;

            //validation
            // $error = $validator->isValidStatisticUserInput($safePost);
            // $error = 'abc';
            
            if ($error == '') {

                $this->stockService->inviteToMeeting($hauptversammlung_id, $safePost);

                return $this->redirectToRoute('stock_allmeeting', [
                    //  'paramName' => 'value'
                ]);
            } 

            $betreff = $safePost->get('betreff');
            $tempId  = $safePost->get('template');
        }

        if ($request->isMethod('GET')) {
            $betreff = "Ladung zu Sammlung";
            $tempId  = $temps[0]->mail_template_id;
            $error   = '';
        }

        return $this->render('stock/meetinginvite.html.twig', [
            'hauptversammlung_id' => $hauptversammlung_id,
            'tempId'  => $tempId,
            'betreff' => $betreff,
            'temps'   => $temps,
            'stories' => $stories,
            'error'   => $error
        ]);
    }

}