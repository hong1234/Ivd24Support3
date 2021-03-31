<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Dao\BUserDao;
use App\Service\BcUserService;

/**
 *
 * @Route(path="/support")
 */
class BcUserController extends AbstractController
{
    /**
     * @Route("/bcuser", name="bcuser_list")
     */
    public function bcuserList(BcUserService $bcUserService){

        $rows = $bcUserService->BcUserList();
        return $this->render('bcuser/list.html.twig', [
            'dataSet' => $rows
        ]);
    }

    /**
     * @Route("/bcuser/{uid}/edit", name="bcuser_edit", requirements={"uid"="\d+"})
     */
    public function bcuserEdit($uid, Request $request, BUserDao $bcuDao){
        
        $user_id = $uid;

        if ($request->isMethod('POST') && $request->request->get('savebutton')){
            //$safePost = filter_input_array(INPUT_POST);
            //var_dump($safePost); exit;

            $safePost = $request->request;
            $userid             = $safePost->get('userid');                 
            $buchungsdatum      = $safePost->get('buchungsdatum');          //: 2021-01-15           
            $firma              = $safePost->get('firma');                  
            $seo_url            = $safePost->get('seo_url');                //eurich-immobilien
            $start_abo          = $safePost->get('start_abo');               //: 2021-02-01
            $end_abo            = $safePost->get('end_abo');                 //: 2022-01-31
            $total_amount       = $safePost->get('total_amount');            //: 360
            $paket_id           = $safePost->get('paket_id');                //: 2
            $paid               = $safePost->get('paid');
            $email              = $safePost->get('email');                   //: info@eurich-immobilien.de
            $end_grundriss      = $safePost->get('end_grundriss');           //: 2022-01-31
            $grundriss_voucher  = $safePost->get('grundriss_voucher');
            $geraete            = $safePost->get('geraete');                 //: 1

            $bcuDao->updateBUser([
                'userid'            => $userid,
                'buchungsdatum'     => $buchungsdatum,
                'firma'             => $firma,
                'seo_url'           => $seo_url,
                'start_abo'         => $start_abo,
                'end_abo'           => $end_abo,
                'total_amount'      => $total_amount,
                'paket_id'          => $paket_id,
                'paid'              => $paid,
                'email'             => $email,
                'end_grundriss'     => $end_grundriss,
                'grundriss_voucher' => $grundriss_voucher,
                'geraete'           => $geraete,
                'user_id'           => $user_id
            ]);

        }

        $row_user  = $bcuDao->getBUser([
            'user_id' => $user_id
        ]);
        //var_dump($row_user);exit;

        return $this->render('bcuser/edit.html.twig', [
            'user_id'   => $user_id,
            'bcuser'    => $row_user
        ]);
    }

    /**
     * @Route("/bcuser/delete", name="bcuser_delete_list")
     */
    public function bcuserDelList(BcUserService $bcUserService){

        $rows = $bcUserService->BcUserDelList();
        return $this->render('bcuser/del.list.html.twig', [
            'dataSet' => $rows
        ]);
    }

    /**
     * @Route("/bcuser/{uid}/delete", name="bcuser_delete", requirements={"uid"="\d+"})
     */
    public function bcuserDelete($uid, Request $request, BUserDao $bcuDao){
        
        $user_id = $uid;

        if ($request->isMethod('POST')){

            if($request->request->get('savebutton')){
                $bcuDao->deleteBUser([
                    'user_id' => $user_id
                ]);
            }
            return $this->redirectToRoute('bcuser_delete_list', [
                //'paramName' => 'value'
            ]);
        }
            
        $row_user  = $bcuDao->getBUser([
            'user_id' => $user_id
        ]);

        return $this->render('bcuser/del.html.twig', [
            'user_id'   => $user_id,
            'bcuser'    => $row_user
        ]);
    }

}