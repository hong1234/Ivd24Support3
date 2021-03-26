<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
// use App\Repository\ProductRepository;

use App\Dao\UserDao;
use App\Service\StatisticUserService;
use App\Service\SupporterService;
use App\Service\UserAccount;

/**
 *
 * @Route(path="/admin")
 */
class StatisticUserController extends AbstractController
{
    /**
     * @Route("/statisticuser", name="statisticuser_list")
     */
    public function statisticuserList(UserDao $uDao)
    {
        $stmt = $uDao->getAllStatisticUser();
        $rows = array();
        while ($row = $stmt->fetch()) {
            $row2 = array();
            $row2[] = $row['user_id'];
            $row2[] = $row['username'];
            $row2[] = $row['email'];
            $row2[] = $row['geschaeftsstelle'];
            $row2[] = date("Y-m-d", (int)$row['registrierungsdatum']);
            $row2[] = date("Y-m-d", (int)$row['lastlogin']);
            $row2[] = "<a href=".$this->generateUrl('statisticuser_edit', array('uid' => $row['user_id'])).">Daten bearbeiten</a>";
            $row2[] = "<a href=".$this->generateUrl('statisticuser_delete', array('uid' => $row['user_id'])).">Statistic-User löschen</a>";
            
            $rows[] = $row2;
        }

        return $this->render('statisticuser/list.html.twig', [
            'dataSet' => $rows
        ]);
    }

    /**
     * @Route("/statisticuser/new", name="statisticuser_new")
     */
    public function newStatisticUser(Request $request, UserDao $uDao, UserAccount $accSer, StatisticUserService $staSer)
    {
        $username = '';
        $email    = '';
        $passwort = '';
        $gs_id    = '1';
        $error    = '';
        $geschaeftsstelle = $uDao->getAllGeschaeftsstelle();

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            //post parameters
            //$safePost = filter_input_array(INPUT_POST);
            //var_dump($safePost); exit;
            $safePost = $request->request;

            $username  =  $safePost->get('username');
            $email     =  $safePost->get('email');
            $passwort  =  $safePost->get('passwort');
            $gs_id     =  $safePost->get('geschaeftsstelle');

            $error = $accSer-> isValidAccountName($username, $email, $passwort);
            
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
    public function statisticuserEdit($uid, Request $request, UserDao $uDao, UserAccount $accSer, StatisticUserService $staSer)
    {
        $user_id = $uid;
        $username = '';
        $email    = '';
        $passwort = '';
        $gs_id    = '1';
        $error    = '';
        $geschaeftsstellen = $uDao->getAllGeschaeftsstelle();

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            // post parameters
            $safePost = $request->request;
            $username = $safePost->get('username');
            $email    = $safePost->get('email');
            $passwort  =  $safePost->get('passwort');
            $gs_id    = $safePost->get('geschaeftsstelle');

            $error = $accSer-> isValidAccountNameByUpdate($user_id, $username, $email, $passwort);
            
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
            'user_id'             => $user_id,
            'username'            => $username,
            'email'               => $email,
            'geschaeftsstelleId'  => $gs_id,
            'geschaeftsstellen'   => $geschaeftsstellen,
            'error'              => $error
        ]);

    }

    /**
     * @Route("/statisticuser/{uid}/delete", name="statisticuser_delete", requirements={"uid"="\d+"})
     */
    public function statisticuserDelete($uid, Request $request, UserDao $uDao, SupporterService $staSer)
    {
        $user_id = $uid;

        if ($request->isMethod('POST')){
            
            if($request->request->get('savebutton')){
                $staSer->deleteSupporter($user_id);
            }
            return $this->redirectToRoute('statisticuser_list', [
                //'paramName' => 'value'
            ]);
        }

        $suser = $uDao->getStatisticUser([
            'user_id' => $user_id
        ]);

        return $this->render('statisticuser/del.html.twig', [
            'user_id'           => $user_id,
            'username'          => $suser['username'],
            'email'             => $suser['email'],
            'geschaeftsstelle'  => $suser['gs_name']
        ]);
    }

}