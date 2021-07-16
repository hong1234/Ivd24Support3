<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Service\InteressentService;
use App\Dao\UserDao;

/**
 *
 * @Route(path="/support")
 */
class InteressentController extends AbstractController
{
    private $uDao;
    private $intService;
    
    public function __construct(UserDao $uDao, InteressentService $intService)
    {
        $this->uDao = $uDao;
        $this->intService = $intService;
    }

    /**
     * @Route("/interessent/{uid}/edit", name="interessent_edit", requirements={"uid"="\d+"})
     */
    public function interessentEdit($uid, Request $request)
    {
        $user_id = $uid; 
        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            // $safePost = filter_input_array(INPUT_POST); // post parameters
            $safePost = $request->request;
            $this->intService->interessentUpdate($user_id, $safePost);
            return $this->redirectToRoute('interessent_list', [
                //'paramName' => 'value'
            ]);
        }

        $interessent = $this->intService->getInteressentData($user_id);
        return $this->render('interessent/edit.html.twig', [
            'user_id'     => $user_id,
            'interessent' => $interessent 
        ]);
    }

    /**
     * @Route("/interessent/{uid}/delete", name="interessent_delete", requirements={"uid"="\d+"})
     */
    public function interessentDelete($uid, Request $request)
    {
        $user_id = $uid;
        if ($request->isMethod('POST')) {
            if ($request->request->get('savebutton')) {
                $this->intService->interessentDelete($user_id);     
            }
            return $this->redirectToRoute('interessent_delete_list', [
                //'paramName' => 'value'
            ]);
        }

        $interessent = $this->intService->getInteressentData($user_id);
        return $this->render('interessent/del.html.twig', [
            'user_id'     => $user_id,
            'interessent' => $interessent
        ]);

    }

    /**
     * @Route("/interessent", name="interessent_list")
     */
    public function interessentList()
    {
        $rows = $this->intService->InteressentList();
        return $this->render('interessent/list.html.twig', [
            'dataSet' => $rows
        ]);
    }

    /**
     * @Route("/interessent/delete", name="interessent_delete_list")
     */
    public function interessentDelList()
    {
        $rows = $this->intService->InteressentDelList();
        return $this->render('interessent/del.list.html.twig', [
            'dataSet' => $rows
        ]);
    }

    /**
     * @Route("/interessent/{uid}/lock", name="interessent_lock", requirements={"uid"="\d+"})
     */
    public function interessentLock($uid)
    {
        $user_id  = $uid;
        $this->uDao->updateUserAccountGesperrt([
            'gesperrt' => 1,
            'user_id'  => $user_id
        ]);
        
        return $this->redirectToRoute('interessent_list', [
            //'paramName' => 'value'
        ]);
    }

    /**
     * @Route("/interessent/{uid}/unlock", name="interessent_unlock", requirements={"uid"="\d+"})
     */
    public function interessentUnLock($uid)
    {
        $user_id  = $uid;
        $this->uDao->updateUserAccountGesperrt([
            'gesperrt' => 0,
            'user_id'  => $user_id
        ]);
        
        return $this->redirectToRoute('interessent_list', [
            //'paramName' => 'value'
        ]);
    }

    /**
     * @Route("/interessent/{uid}/pwedit", name="interessent_pw_edit", requirements={"uid"="\d+"})
     */
    public function interessentPwEdit($uid, Request $request)
    {
        $user_id = $uid; 

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {

            $safePost = $request->request;
            $passwort   = $safePost->get('passwort');
            $crypt_passwort = md5($passwort);

            $this->uDao->updateUserAccountPW([
                'crypt_passwort' => $crypt_passwort,
                'user_id'        => $user_id
            ]); 
            
            return $this->redirectToRoute('interessent_list', [
                //'paramName' => 'value'
            ]);
        }
        
        $user_account = $this->uDao->getRowInTableByIdentifier('user_account', [
            'user_id' => $user_id
        ]);
        $username = $user_account['username'];

        return $this->render('interessent/pwedit.html.twig', [
            'user_id'  => $user_id,
            'username' => $username
        ]);
    }

    /**
     * @Route("/interessent/{uid}/delete/undo", name="interessent_delete_undo", requirements={"uid"="\d+"})
     */
    public function deleteUndo($uid)
    {
        $user_id = $uid;
        $this->uDao->updateUserAccountLoeschung([
            'user_id' => $user_id
        ]);

        return $this->redirectToRoute('interessent_delete_list', [
            //'paramName' => 'value'
        ]);
    }

}