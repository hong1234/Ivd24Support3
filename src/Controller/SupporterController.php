<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
// use App\Repository\ProductRepository;

use App\Validator\UserAccount;

use App\Dao\UserDao;
use App\Service\SupporterService;

/**
 *
 * @Route(path="/admin")
 */
class SupporterController extends AbstractController
{
    /**
     * @Route("/supporter", name="supporter_list")
     */
    public function supporterList(SupporterService $supSer) {
        $rows = $supSer->SupporterList();
        return $this->render('supporter/list.html.twig', [
            'dataSet' => $rows
        ]);
    }

    /**
     * @Route("/supporter/new", name="supporter_new")
     */
    public function newSupporter(Request $request, UserAccount $validator, SupporterService $supSer)
    {
        // $manager = $this->getDoctrine()->getManager();
        $username = '';
        $email    = '';
        $passwort = '';
        $error    = '';

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {

            $safePost = $request->request;

            $username = $safePost->get('username');  //validation
            $email    = $safePost->get('email');     //validation
            $passwort = $safePost->get('passwort');

            //validation
            $empty1 = $validator->isEmptyUsername($username);
            $empty2 = $validator->isEmptyEmail($email);
            $empty3 = $validator->isEmptyPasswort($passwort);
            $error1 = $empty1.$empty2.$empty3;

            $error2 = $validator->isValidEmail($email);
            $error  = $error1.$error2;
            
            if ($error == '') {
                $supSer->newSupporter($safePost);
                return $this->redirectToRoute('supporter_list', [
                    //'paramName' => 'value'
                ]);   
            } 

        }

        return $this->render('supporter/new.html.twig', [
            'username' => $username,
            'email'    => $email,
            'passwort' => $passwort,
            'error'    => $error
        ]);
    }

    /**
     * @Route("/supporter/{uid}/edit", name="supporter_edit", requirements={"uid"="\d+"})
     */
    public function supporterEdit($uid, Request $request, UserDao $uDao, UserAccount $validator, SupporterService $supSer)
    {
        $user_id  = $uid;
        $username = '';
        $email    = '';
        $passwort = '';
        $error    = '';

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            // post parameters
            //$safePost = filter_input_array(INPUT_POST);
            //var_dump($safePost); exit;
            $safePost = $request->request;

            $username = $safePost->get('username');
            $email    = $safePost->get('email');
            $passwort = $safePost->get('passwort');

            //validation
            $empty1 = $validator->isEmptyUsername($username);
            $empty2 = $validator->isEmptyEmail($email);
            $empty3 = $validator->isEmptyPasswort($passwort);
            $error1 = $empty1.$empty2.$empty3;
            
            $error2 = $validator->isValidEmailByUpdate($user_id, $email);
            $error  = $error1.$error2;
            
            if ($error == '') {
                $supSer->updateSupporter($user_id, $safePost);
                return $this->redirectToRoute('supporter_list', [
                    //'paramName' => 'value'
                ]);  
            } 
             
        }

        if ($request->isMethod('GET')) {
            $suser = $uDao->getSupportUser([
                'user_id' => $user_id
            ]);

            $username = $suser['username'];
            $email    = $suser['email'];
        }

        return $this->render('supporter/edit.html.twig', [
            'user_id'  => $user_id,
            'username' => $username,
            'email'    => $email,
            'passwort' => $passwort,
            'error'    => $error
        ]);
    }

    /**
     * @Route("/supporter/{uid}/delete", name="supporter_delete", requirements={"uid"="\d+"})
     */
    public function supporterDelete($uid, Request $request, UserDao $uDao, SupporterService $supSer)
    {
        $user_id = $uid;
        if ($request->isMethod('POST')){
            if($request->request->get('savebutton')){  
                $supSer->deleteSupporter($user_id);
            }
            return $this->redirectToRoute('supporter_list', [
                //'paramName' => 'value'
            ]);
        }

        $suser = $uDao->getSupportUser([
            'user_id' => $user_id
        ]);

        return $this->render('supporter/del.html.twig', [
            'user_id'  => $user_id,
            'username' => $suser['username'],
            'email'    => $suser['email']
        ]);
    }
}