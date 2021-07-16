<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Service\StatisticUserService;
use App\Service\StringFormat;
use App\Validator\UserAccount;

use App\Dao\UserDao;

/**
 *
 * @Route(path="/admin")
 */
class StatisticUserController extends AbstractController
{
    private $uDao;
    private $suService;
    
    public function __construct(UserDao $uDao, StatisticUserService $suService)
    {
        $this->uDao = $uDao;
        $this->suService = $suService;
    }

    /**
     * @Route("/statisticuser", name="statisticuser_list")
     */
    public function statisticuserList() {
        $rows = $this->suService->StatisticUserList();
        return $this->render('statisticuser/list.html.twig', [
            'dataSet' => $rows
        ]);
    }

    /**
     * @Route("/statisticuser/new", name="statisticuser_new")
     */
    public function newStatisticUser(Request $request, UserAccount $validator, StringFormat $sfService)
    {
        $error = '';

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            //post parameters
            //$safePost = filter_input_array(INPUT_POST);
            $safePost = $request->request;

            //validation
            $error = $validator->isValidStatisticUserInput($safePost);
            
            if ($error == '') {
                $this->suService->newStatisticUser($safePost);
                return $this->redirectToRoute('statisticuser_list', [
                    //'paramName' => 'value'
                ]);
            } 
            
        }

        if ($request->isMethod('GET')) {
            $username = '';
            $email    = '';
            $passwort = $sfService->getRandPassWord();
            $gs_id    = '1';
            $error    = '';
        }

        $geschaeftsstelle = $this->uDao->getAllRowsInTable('user_geschaeftsstelle');
        return $this->render('statisticuser/new.html.twig', [
            'username'           => $username,
            'email'              => $email,
            'passwort'           => $passwort,
            'geschaeftsstelleId' => $gs_id,
            'geschaeftsstelle'   => $geschaeftsstelle,
            'error'              => $error
        ]);

    }

    /**
     * @Route("/statisticuser/{uid}/edit", name="statisticuser_edit", requirements={"uid"="\d+"})
     */
    public function statisticUserEdit($uid, Request $request, UserAccount $validator)
    {
        $user_id = $uid;
        $username = '';
        $email    = '';
        $passwort = '';
        $gs_id    = '1';
        $error    = '';
        
        $geschaeftsstellen = $this->uDao->getAllRowsInTable('user_geschaeftsstelle');

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            // post parameters
            $safePost = $request->request;
            //validation
            $error = $validator->isValidStatisticUserInputByUpdate($user_id, $safePost);
            
            if ($error == '') {
                $this->suService->updateStatisticUser($user_id, $safePost);
                return $this->redirectToRoute('statisticuser_list', [
                    //'paramName' => 'value'
                ]);     
            } 
        }

        if ($request->isMethod('GET')) {
            $suser = $this->uDao->getStatisticUser([
                'user_id' => $user_id
            ]);

            $username = $suser['username'];
            $email    = $suser['email'];
            $gs_id    = $suser['geschaeftsstelleId'];
        }

        return $this->render('statisticuser/edit.html.twig', [
            'user_id'            => $user_id,
            'username'           => $username,
            'email'              => $email,
            'geschaeftsstelleId' => $gs_id,
            'geschaeftsstellen'  => $geschaeftsstellen,
            'error'              => $error
        ]);

    }

    /**
     * @Route("/statisticuser/{uid}/delete", name="statisticuser_delete", requirements={"uid"="\d+"})
     */
    public function statisticuserDelete($uid, Request $request)
    {
        $user_id = $uid;

        if ($request->isMethod('POST')){
            
            if($request->request->get('savebutton')){
                $this->suService->deleteStatisticUser($user_id);
            }
            return $this->redirectToRoute('statisticuser_list', [
                //'paramName' => 'value'
            ]);
        }

        $suser = $this->uDao->getStatisticUser([
            'user_id' => $user_id
        ]);

        return $this->render('statisticuser/del.html.twig', [
            'user_id'          => $user_id,
            'username'         => $suser['username'],
            'email'            => $suser['email'],
            'geschaeftsstelle' => $suser['gs_name']
        ]);
    }

}