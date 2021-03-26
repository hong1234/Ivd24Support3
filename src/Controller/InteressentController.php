<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Dao\UserDao;
use App\Dao\InteressentDao;
use App\Service\InteressentService;

/**
 *
 * @Route(path="/support")
 */
class InteressentController extends AbstractController
{
    /**
     * @Route("/interessent", name="interessent_list")
     */
    public function interessentList(InteressentDao $iDao){

        $stmt = $iDao->getAllInteressent();

        $rows = array();
        while ($row = $stmt->fetch()) {    
            $row2 = array();

            $row2[] = $row['userId'];
            $row2[] = $row['vorname'].' '.$row['name']; 
            $row2[] = $row['firma']; 
            $row2[] = $row['userEmail']; 
            $row2[] = date("Y-m-d", (int)$row['registrierungsdatum']);    //=> string '1438597868' (length=10)
            $row2[] = date("Y-m-d", (int)$row['lastlogin']);              // => string '1438954407' (length=10)

            $str1 = "<a href=".$this->generateUrl('interessent_edit', array('uid' => $row['userId'])).">Bearbeiten</a><br>";
            $str2 = "<a href=".$this->generateUrl('interessent_pw_edit', array('uid' => $row['userId'])).">Passwort bearbeiten</a><br>";
            $str3 = "";
            if($row['gesperrt']==1){        
                $str3 = "<a href=".$this->generateUrl('interessent_lock_unlock', array('uid' => $row['userId'], 'gesperrt' => 0)).">Account entsperren</a><br>";
            } else {
                $str3 = "<a href=".$this->generateUrl('interessent_lock_unlock', array('uid' => $row['userId'], 'gesperrt' => 1)).">Account sperren</a><br><br>";
            }
            $row2[] = $str1.$str2.$str3;
                          
            $rows[] = $row2;
        }

        return $this->render('interessent/list.html.twig', [
            'dataSet' => $rows
        ]);
    }

    /**
     * @Route("/interessent/{uid}/edit", name="interessent_edit", requirements={"uid"="\d+"})
     */
    public function interessentEdit($uid, Request $request, InteressentDao $iDao, InteressentService $intSer)
    {
        $user_id = $uid; 

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            // post parameters
            // $safePost = filter_input_array(INPUT_POST); 
            // var_dump($safePost); exit;
            $safePost = $request->request;
            $intSer->interessentUpdate($user_id, $safePost);
            return $this->redirectToRoute('interessent_list', [
                //'paramName' => 'value'
            ]);
        }
    
        $user_int = $iDao->getInteressent([
            'user_id' => $user_id
        ]);

        $user_int2 = $iDao->getUserAccount([
            'user_id' => $user_id
        ]);

        return $this->render('interessent/edit.html.twig', [
            'user_id'       => $user_id,
            'interessent'   => array_merge($user_int, $user_int2)  
        ]);
    }

    /**
     * @Route("/interessent/delete", name="interessent_delete_list")
     */
    public function interessentDelList(InteressentDao $iDao){

        $stmt = $iDao->getDelInteressent();
    
        $rows = array();
        while ($row = $stmt->fetch()) {        
            $row2 = array();

            $row2[] = $row['userId']; 
            $row2[] = $row['vorname'].' '.$row['name'];
            $row2[] = $row['firma'];
            $row2[] = $row['userEmail'];
            $row2[] = substr($row['loesch_datum'], 0, 10);
        
            $str1 = "<a href=".$this->generateUrl('interessent_delete', array('uid' => $row['userId'])).">Löschen</a><br>";
            $str2 = "<a href=".$this->generateUrl('interessent_delete_undo', array('uid' => $row['userId'])).">Löschung zurücknehmen</a><br>";
            $row2[] = $str1.$str2;

            $rows[] = $row2;
        }

        return $this->render('interessent/del.list.html.twig', [
            'dataSet' => $rows
        ]);
    }

    /**
     * @Route("/interessent/{uid}/delete", name="interessent_delete", requirements={"uid"="\d+"})
     */
    public function interessentDelete($uid, Request $request, InteressentDao $iDao, InteressentService $intSer)
    {
        $user_id = $uid;

        if ($request->isMethod('POST')) {

            if ($request->request->get('savebutton')) {

                $intSer->interessentDelete($user_id);
                return $this->redirectToRoute('interessent_delete_list', [
                    //'paramName' => 'value'
                ]);     
            }
        }

        $user_int = $iDao->getInteressent([
            'user_id' => $user_id
        ]);

        $user_int2 = $iDao->getUserAccount([
            'user_id' => $user_id
        ]);

        return $this->render('interessent/del.html.twig', [
            'user_id'      => $user_id,
            'interessent'  => array_merge($user_int, $user_int2)
        ]);

    }

    /**
     * @Route("/interessent/{uid}/lock/{gesperrt}", name="interessent_lock_unlock", requirements={"uid"="\d+"})
     */
    public function interessentLock($uid, $gesperrt, UserDao $uDao){
        $user_id  = $uid;
        $uDao->updateUserAccountGesperrt([
            'gesperrt' => $gesperrt,
            'user_id'  => $user_id
        ]);
        
        return $this->redirectToRoute('interessent_list', [
            //'paramName' => 'value'
        ]);
    }

    /**
     * @Route("/interessent/{uid}/pwedit", name="interessent_pw_edit", requirements={"uid"="\d+"})
     */
    public function interessentPwEdit($uid, Request $request, UserDao $uDao)
    {
        $user_id = $uid; 

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {

            $safePost = $request->request;
            $passwort   = $safePost->get('passwort');
            $crypt_passwort = md5($passwort);

            $uDao->updateUserAccountPW([
                'crypt_passwort' => $crypt_passwort,
                'user_id'        => $user_id
            ]); 
            
            return $this->redirectToRoute('interessent_list', [
                //'paramName' => 'value'
            ]);
        }

        $user_makler = $uDao->getUserAccount([
            'user_id' => $user_id
        ]);
        $username = $user_makler['username']; 

        return $this->render('interessent/pwedit.html.twig', [
            'user_id'    => $user_id,
            'username'   => $username
        ]);
    }

    /**
     * @Route("/interessent/{uid}/delete/undo", name="interessent_delete_undo", requirements={"uid"="\d+"})
     */
    public function deleteUndo($uid, UserDao $uDao){
        $user_id = $uid;
        $uDao->updateUserAccountLoeschung([
            'user_id' => $user_id
        ]);

        return $this->redirectToRoute('interessent_delete_list', [
            //'paramName' => 'value'
        ]);
    }

}