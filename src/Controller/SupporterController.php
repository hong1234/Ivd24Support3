<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Dao\UserDao;
use App\Service\SupporterService;
use App\Service\StringFormat;
use App\Validator\UserAccount;

/**
 *
 * @Route(path="/admin")
 */
class SupporterController extends AbstractController
{
    private $uDao;
    private $supService;
    
    public function __construct(UserDao $uDao, SupporterService $supService)
    {
        $this->uDao = $uDao;
        $this->supService = $supService;
    }

    /**
     * @Route("/supporter", name="supporter_list")
     */
    public function supporterList() {
        $rows = $this->supService->SupporterList();
        return $this->render('supporter/list.html.twig', [
            'dataSet' => $rows
        ]);
    }

    /**
     * @Route("/supporter/new", name="supporter_new")
     */
    public function newSupporter(Request $request, UserAccount $validator, StringFormat $sfService)
    {
        // $manager = $this->getDoctrine()->getManager();
        $error = '';

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {

            $safePost = $request->request;
            //validation
            $error = $validator->isValidSupporterInput($safePost);

            if ($error == '') {
                $this->supService->newSupporter($safePost);
                return $this->redirectToRoute('supporter_list', [
                    //'paramName' => 'value'
                ]);   
            }
            
            $username = $safePost->get('username');
            $email    = $safePost->get('email');
            $passwort = $safePost->get('passwort');

        }

        if ($request->isMethod('GET')) {
            $username = '';
            $email    = '';
            $passwort = $sfService->getRandPassWord();
            $error    = '';
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
    public function supporterEdit($uid, Request $request, UserAccount $validator)
    {
        $user_id  = $uid;
        $error = '';

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            // post parameters
            //$safePost = filter_input_array(INPUT_POST);
            $safePost = $request->request;
            //validation
            $error = $validator->isValidSupporterInputByUpdate($user_id, $safePost);
            
            if ($error == '') {
                $this->supService->updateSupporter($user_id, $safePost);
                return $this->redirectToRoute('supporter_list', [
                    //'paramName' => 'value'
                ]);  
            }
            
            $username = $safePost->get('username');
            $email    = $safePost->get('email');
            $passwort = $safePost->get('passwort');
             
        }

        if ($request->isMethod('GET')) {
            $suser = $this->uDao->getSupportUser([
                'user_id' => $user_id
            ]);

            $username = $suser['username'];
            $email = $suser['email'];
            $passwort = '';
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
    public function supporterDelete($uid, Request $request)
    {
        $user_id = $uid;
        if ($request->isMethod('POST')){
            if($request->request->get('savebutton')){  
                $this->supService->deleteSupporter($user_id);
            }
            return $this->redirectToRoute('supporter_list', [
                //'paramName' => 'value'
            ]);
        }

        $suser = $this->uDao->getSupportUser([
            'user_id' => $user_id
        ]);

        return $this->render('supporter/del.html.twig', [
            'user_id'  => $user_id,
            'username' => $suser['username'],
            'email'    => $suser['email']
        ]);
    }
}