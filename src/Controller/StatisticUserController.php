<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
// use App\Repository\ProductRepository;

use App\Validator\UserAccount;

use App\Dao\UserDao;
use App\Service\StatisticUserService;


/**
 *
 * @Route(path="/admin")
 */
class StatisticUserController extends AbstractController
{
    /**
     * @Route("/statisticuser", name="statisticuser_list")
     */
    public function statisticuserList(StatisticUserService $staSer) {
        $rows = $staSer->StatisticUserList();
        return $this->render('statisticuser/list.html.twig', [
            'dataSet' => $rows
        ]);
    }

    /**
     * @Route("/statisticuser/new", name="statisticuser_new")
     */
    public function newStatisticUser(Request $request, UserDao $uDao, UserAccount $validator, StatisticUserService $staSer)
    {
        $username = '';
        $email    = '';
        $passwort = '';
        $gs_id    = '1';
        $error    = '';
        
        $geschaeftsstelle = $uDao->getAllRowsInTable('user_geschaeftsstelle');

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            //post parameters
            //$safePost = filter_input_array(INPUT_POST);
            //var_dump($safePost); exit;
            $safePost = $request->request;

            $username = $safePost->get('username');
            $email    = $safePost->get('email');
            $passwort = $safePost->get('passwort');
            $gs_id    = $safePost->get('geschaeftsstelle');

            //validation
            $empty1 = $validator->isEmptyUsername($username);
            $empty2 = $validator->isEmptyEmail($email);
            $empty3 = $validator->isEmptyPasswort($passwort);
            $error1 = $empty1.$empty2.$empty3;
            
            $error2 = $validator->isValidEmail($email);
            $error  = $error1.$error2;
            
            if ($error == '') {
                $staSer->newStatisticUser($safePost);
                return $this->redirectToRoute('statisticuser_list', [
                    //'paramName' => 'value'
                ]);
            } 
            
        }

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
    public function statisticuserEdit($uid, Request $request, UserDao $uDao, UserAccount $validator, StatisticUserService $staSer)
    {
        $user_id = $uid;
        $username = '';
        $email    = '';
        $passwort = '';
        $gs_id    = '1';
        $error    = '';
        
        $geschaeftsstellen = $uDao->getAllRowsInTable('user_geschaeftsstelle');

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            // post parameters
            $safePost = $request->request;

            $username = $safePost->get('username');
            $email    = $safePost->get('email');
            $passwort = $safePost->get('passwort');
            $gs_id    = $safePost->get('geschaeftsstelle');

            //validation
            $empty1 = $validator->isEmptyUsername($username);
            $empty2 = $validator->isEmptyEmail($email);
            $empty3 = $validator->isEmptyPasswort($passwort);
            $error1 = $empty1.$empty2.$empty3;

            $error2 = $validator->isValidEmailByUpdate($user_id, $email);
            $error  = $error1.$error2;
            
            if ($error == '') {
                $staSer->updateStatisticUser($user_id, $safePost);
                return $this->redirectToRoute('statisticuser_list', [
                    //'paramName' => 'value'
                ]);     
            } 
        }

        if ($request->isMethod('GET')) {
            $suser = $uDao->getStatisticUser([
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
    public function statisticuserDelete($uid, Request $request, UserDao $uDao, StatisticUserService $staSer)
    {
        $user_id = $uid;

        if ($request->isMethod('POST')){
            
            if($request->request->get('savebutton')){
                $staSer->deleteStatisticUser($user_id);
            }
            return $this->redirectToRoute('statisticuser_list', [
                //'paramName' => 'value'
            ]);
        }

        $suser = $uDao->getStatisticUser([
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