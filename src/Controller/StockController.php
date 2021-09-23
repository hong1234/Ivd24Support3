<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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

    //------verify----------

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
        $error = '';

        $docs = $this->stockService->aktienDoc($userid);
        $makler = $this->stockService->maklerData($userid);
        $aktien = $this->stockService->maklerAktien($userid);

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {

            //validation
            // $error = $validator->isValidInput($safePost);
            $i = 0;
            $doc = [];
            while ($i < count($docs))
            {
                $document_cateogory = $docs[$i]['document_cateogory'];
                if($document_cateogory == 'Aktienkaufvertrag'){
                    $doc['Aktienkaufvertrag'] = 1;
                } 

                if($document_cateogory == 'Rechnung'){
                    $doc['Rechnung'] = 1;
                } 
                $i++;
            }

            if(!isset($doc['Aktienkaufvertrag'])){
                $error = $error.'Doc Aktienkaufvertrag.pdf fehlt ---';
            }

            if(!isset($doc['Rechnung'])){
                $error = $error.'Doc Rechnung.pdf fehlt ---';
            }

            if ($error == '') {

                $this->stockService->verifyUserAktien($userid);
                return $this->redirectToRoute('stock_notverified', [
                    //'paramName' => 'value'
                ]);
            }
            
        }

        if ($request->isMethod('GET')) {
            
        }

        return $this->render('stock/verify.html.twig', [
            'user_id' => $userid,
            'makler' => $makler,
            'aktien' => $aktien,
            'docs'  => $docs,
            'error' => $error
        ]);
    }

    //----aktien-documente------------

    /**
     * @Route("/stock/docdownload/{docid}", name="stock_docdownload", requirements={"userid"="\d+", "docid"="\d+"})
     */
    public function docDownload(int $docid)
    {
        $downloaded_file = $this->stockService->getTargetDocPath($docid);

        // if (file_exists($downloaded_file)) {

        //     header('Content-Description: File Transfer');
        //     header('Content-Type: application/octet-stream');
        //     header('Content-Disposition: attachment; filename="'.basename($downloaded_file).'"');
        //     header('Expires: 0');
        //     header('Cache-Control: must-revalidate');
        //     header('Pragma: public');
        //     header('Content-Length: ' . filesize($downloaded_file));
        //     readfile($downloaded_file);
        //     exit;
        // }

        return $this->file($downloaded_file);
    }

    /**
     * @Route("/stock/{userid}/docdelete/{docid}", name="stock_docdelete", requirements={"userid"="\d+", "docid"="\d+"})
     */
    public function docDelete(int $userid, int $docid)
    {
        $this->stockService->deleteAktienDoc($docid);
        return $this->redirectToRoute('stock_verify', [
            'userid' => $userid
        ]);
    }

    /**
     * @Route("/stock/docupload/{userid}", name="stock_docupload", requirements={"userid"="\d+"})
     */
    public function docUpload(int $userid, Request $request)
    {
        $error = '';
       
        $category = '';
        $infos = '';

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            //post parameters
            $safePost = $request->request;
            // var_dump($safePost); 
            // var_dump($_FILES['dokument']); 
            // exit;

            $category = $safePost->get('category');
            $infos    = $safePost->get('infos');

            //validation
            // $error = $validator->isValidInput($safePost);
            // $error = 'failed';

            if ($error == '') {

                if ($_FILES['dokument']['error'] == 0) {
                    
                    //delete docs if exits-----
                    $this->stockService->deleteAktienDoc2($userid, $category);
                    
                    //upload new-doc-----------
                    // $path = $this->getParameter('kernel.project_dir').'/public/dokumente/'.basename($_FILES['dokument']['name']);
                    // $path = '/var/www/html/bilder/1/b00619003/files/'.basename($_FILES['dokument']['name']);

                    $path = $this->stockDao->getDocDirByUserId(['user_id' => $userid])->verzeichnis;
                    if ($category == 'Aktienkaufvertrag') {
                        $dokument_name = 'Aktienkaufvertrag.pdf';
                    } elseif ($category == 'Rechnung') {
                        $dokument_name = 'Rechnung.pdf';
                    } else {
                        $dokument_name = 'Urkunde.pdf';
                    }
                    $target_path = $path.$dokument_name;
    
                    if(move_uploaded_file($_FILES['dokument']['tmp_name'], $target_path)) {

                        $this->stockDao->insertAktienDoc([
                            'user_id' => $userid,
                            'document_cateogory' => $category,
                            'document_name' => $dokument_name,
                            'document_info' => $infos,
                            'document_path' => $path
                        ]);
        
                        return $this->redirectToRoute('stock_verify', [
                            'userid' => $userid
                        ]);
                        
                    } else {
                        $error = $error."an error by uploading the file ---";
                    }

                } else {
                    $error = $error."please select 1 file ---";
                }

            }

        }

        if ($request->isMethod('GET')) {
            
        }

        return $this->render('stock/docupload.html.twig', [
            'user_id'  => $userid,
            'category' => $category,
            'categories' => [
                [
                    'cat_name' => 'Aktienkaufvertrag'
                ],
                [
                    'cat_name' => 'Rechnung'
                ],
                [
                    'cat_name' => 'Urkunde'
                ]
            ],
            'infos' => $infos,
            'error' => $error
        ]);
        
    }

    //-------------------------------

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
        $template_id = 0;
        $betreff = '';
        $mode = 'normal';

        $temps = $this->stockDao->getTemplatesForGeneralMeeting();
        $stories = $this->stockDao->getSendMailTempleteStory();

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            
            //post parameters
            $safePost = $request->request;
            // var_dump( $safePost); exit;

            $betreff = $safePost->get('betreff');
            $template_id = $safePost->get('template');

            if($safePost->get('test_mail') !== null){
                $mode = 'test';
            } 

            //validation
            // $error = $validator->isValidStatisticUserInput($safePost);
            // $error = 'abc';

            if ($error == '') {

                $this->stockService->inviteToMeeting($hauptversammlung_id, $betreff, $template_id, $mode);

                return $this->redirectToRoute('stock_allmeeting', [
                    //  'paramName' => 'value'
                ]);
            } 

        }

        if ($request->isMethod('GET')) {
            $betreff = "Einladung zur Versammlung";
            $template_id = $temps[0]->mail_template_id;
        }

        return $this->render('stock/meetinginvite.html.twig', [
            'hauptversammlung_id' => $hauptversammlung_id,
            'betreff' => $betreff,
            'tempId'  => $template_id,
            'temps'   => $temps,
            'stories' => $stories,
            'error'   => $error
        ]);
    }

    /**
     * @Route("/stock/meeting/{hauptversammlung_id}/close", name="stock_meetingclose", requirements={"hauptversammlung_id"="\d+"})
     */
    public function meetingClose(int $hauptversammlung_id)
    {
        $this->stockDao->updateClosed([
            'meeting_id' => $hauptversammlung_id
        ]);

        return $this->redirectToRoute('stock_meeting', [
            'hauptversammlung_id' => $hauptversammlung_id
        ]);
    }

}