<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
// use App\Repository\ProductRepository;

use App\Dao\UserDao;
use App\Service\StatisticUserService;
use App\Service\SupporterService;

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
        while ($row = $stmt->fetchAssociative()) {
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
    public function newStatisticUser(Request $request, UserDao $uDao, StatisticUserService $stuse)
    {
        $username = '';
        $email    = '';
        $passwort = '';
        $gs_id    = '1';
        $geschaeftsstelle = $uDao->getAllGeschaeftsstelle()->fetchAllAssociative();

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            //post parameters
            //$safePost = filter_input_array(INPUT_POST);
            //var_dump($safePost); exit;

            $safePost = $request->request;

            $username  =  $safePost->get('username');
            $email     =  $safePost->get('email');
            $passwort  =  $safePost->get('passwort');
            $gs_id     =  $safePost->get('geschaeftsstelle');
            
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

                $stuse->newStatisticUser($username, $email, $passwort, $gs_id);
                return $this->redirectToRoute('statisticuser_list', [
                    //'paramName' => 'value'
                ]);
            } 
            else {
                $email = $email."<--Invalid email format";
            }
        }

        return $this->render('statisticuser/new.html.twig', [
            'email'    => $email,
            'username' => $username,
            'passwort' => $passwort,
            'geschaeftsstelleId'  => $gs_id,
            'geschaeftsstelle' => $geschaeftsstelle
        ]);
    }

    /**
     * @Route("/statisticuser/{uid}/edit", name="statisticuser_edit", requirements={"uid"="\d+"})
     */
    public function statisticuserEdit($uid, Request $request, UserDao $uDao, StatisticUserService $stuse)
    {
        $user_id = $uid;
        $geschaeftsstellen = $uDao->getAllGeschaeftsstelle()->fetchAllAssociative();

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            // post parameters
            //$safePost = filter_input_array(INPUT_POST);
            //var_dump($safePost); exit;

            $safePost = $request->request;

            $username = $safePost->get('username');
            $email    = $safePost->get('email');
            $passwort = $safePost->get('passwort');
            $gs_id    = $safePost->get('geschaeftsstelle');

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

                $stuse->updateStatisticUser($user_id, $username, $email, $passwort, $gs_id);
                return $this->redirectToRoute('statisticuser_list', [
                    //'paramName' => 'value'
                ]);    
            } 
            else {
                 $email = $email."--Invalid email format";
            }
        }

        if ($request->isMethod('GET')) {
            $suser = $uDao->getStatisticUser([
                'user_id' => $user_id
            ]);

            $username = $suser['username'];
            $email    = $suser['email'];
            //$passwort = $safePost->get('passwort');
            $gs_id    = $suser['geschaeftsstelleId'];

        }

        return $this->render('statisticuser/edit.html.twig', [
            'user_id'             => $user_id,
            'username'            => $username,
            'email'               => $email,
            'geschaeftsstelleId'  => $gs_id,
            'geschaeftsstellen'   => $geschaeftsstellen
        ]);

    }

    /**
     * @Route("/statisticuser/{uid}/delete", name="statisticuser_delete", requirements={"uid"="\d+"})
     */
    public function statisticuserDelete($uid, Request $request, UserDao $uDao, SupporterService $supSer)
    {
        $user_id = $uid;

        if ($request->isMethod('POST')){
            
            if($request->request->get('savebutton')){
                $supSer->deleteSupporter($user_id);
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