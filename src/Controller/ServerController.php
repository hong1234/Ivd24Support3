<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Dao\ServerDao;
use App\Service\FtpServer;

/**
 *
 * @Route(path="")
 */
class ServerController extends AbstractController
{
    /**
     * @Route("/server", name="server_list")
     */
    public function serverList(FtpServer $ftp){
        $rows = $ftp->FtpList();
        return $this->render('server/list.html.twig', [
            'dataSet' => $rows
        ]);
    }

     /**
     * @Route("/server/{uid}/edit", name="server_edit", requirements={"uid"="\d+"})
     */
    public function serverEdit($uid, Request $request, ServerDao $sDao){ 
        $user_id = $uid;

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            // post parameters
            // $safePost = filter_input_array(INPUT_POST);
            // var_dump($safePost); exit;

            $safePost = $request->request;
            $ftp_pause    = $safePost->get('ftp_pause');
            $after_break  = $safePost->get('after_break');

            $sDao->updateServerConfig([
                'ftp_pause' => $ftp_pause,
                'ftp_import_after_break' => $after_break,
                'user_id' => $user_id
            ]);
            
            return $this->redirectToRoute('server_list', [
                //'paramName' => 'value'
            ]); 
        }

        $makler_server  = $sDao->getServerOfMakler([
            'user_id' => $user_id
        ]);
        //var_dump($makler_server);exit;

        return $this->render('server/edit.html.twig', [
            'user_id'   => $user_id,
            'server'    => $makler_server
        ]);
    }

    /**
     * @Route("/server/pause", name="server_pause_list")
     */
    public function serverPauseList(FtpServer $ftp){

        $rows = $ftp->FtpPauseList();
        return $this->render('server/pause.list.html.twig', [
            'dataSet' => $rows
        ]);
    }

    /**
     * @Route("/server/{uid}/download/{file}", name="server_download_file")
     */
    public function serverFileDownload($uid, $file, FtpServer $ftp)
    {
        $downloaded_file = $ftp->fileDownloadToApp($uid, $file);
        if (file_exists($downloaded_file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($downloaded_file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($downloaded_file));
            readfile($downloaded_file);
            //exit;
            unlink($downloaded_file);
        }
        return $this->redirectToRoute('server_pause_list', [
            //'paramName' => 'value'
        ]);
    }

    /**
     * @Route("/server/{uid}/delete/{file}", name="server_delete_file", requirements={"uid"="\d+"})
     */
    public function serverFileDelete($uid, $file, FtpServer $ftp){
        $ftp->deleteFileOnFtpServer($uid, $file);
        return $this->redirectToRoute('server_pause_list', [
             //'paramName' => 'value'
        ]);
    }

    /**
     * @Route("/server/{uid}/starten", name="server_starten", requirements={"uid"="\d+"})
     */
    public function serverStarten($uid, ServerDao $sDao){
        
        $user_id = $uid;

        $sDao->updateServerConfig([
            'ftp_pause' => 'N',
            'ftp_import_after_break' => '0',
            'user_id' => $user_id
        ]);
        
        return $this->redirectToRoute('server_pause_list', [
            //'paramName' => 'value'
        ]);
    }

}