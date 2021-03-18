<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Dao\UserDao;

/**
 *
 * @Route(path="/support")
 */
class InteressentController extends AbstractController
{
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
            //$user_id    = $safePost->get('userid');
             //$benutzer   = $safePost->get('user');
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
     * @Route("/interessent/{uid}/edit", name="interessent_edit", requirements={"uid"="\d+"})
     */
    public function interessentEdit($uid, Request $request, UserDao $uDao)
    {
        $user_id = $uid; 

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            // post parameters
            // $safePost = filter_input_array(INPUT_POST); 
            // var_dump($safePost); exit;

            $safePost = $request->request;
            //$request->request->get('name');
            $email        = $safePost->get('email');
            $anrede       = $safePost->get('anrede');
            $titel        = $safePost->get('titel');
            $namenstitel  = $safePost->get('namenstitel');
            $vorname      = $safePost->get('vorname');
            $name         = $safePost->get('name');
            $firma        = $safePost->get('firma');
            $strasse      = $safePost->get('strasse');
            $plz          = $safePost->get('plz');
            $ort          = $safePost->get('ort');
            $telefon      = $safePost->get('telefon');
            $telefax      = $safePost->get('telefax');
            $homepage     = $safePost->get('homepage');
		    $mobil        = $safePost->get('mobil');

            $em = $uDao->getEm();
            $em->getConnection()->beginTransaction();
            try {
                $uDao->updateUserAccountEmail([
                    'email'     => $email,
                    'user_id'   => $user_id
                ]);
            
                $uDao->updateUserInteressent([
                    'anrede'        => $anrede, 
                    'titel'         => $titel, 
                    'namenstitel'   => $namenstitel, 
                    'name'          => $name, 
                    'vorname'       => $vorname, 
                    'firma'         => $firma, 
                    'strasse'       => $strasse, 
                    'plz'           => $plz, 
                    'ort'           => $ort,  
                    'email'         => $email, 
                    'telefon'       => $telefon, 
                    'telefax'       => $telefax, 
                    'homepage'      => $homepage, 
                    'mobil'         => $mobil,
                    'user_id'       => $user_id
                ]);
          
                $em->getConnection()->commit();     
          
            } catch (\Exception $e) {
                $em->getConnection()->rollBack();
                throw $e;
            }

            return $this->redirectToRoute('interessent_list', [
                //'paramName' => 'value'
            ]);
        }
    
        $user_int = $uDao->getInteressent([
            'user_id' => $user_id
        ]);

        $user_int2 = $uDao->getUserAccount([
            'user_id' => $user_id
        ]);

        return $this->render('interessent/edit.html.twig', [
            'user_id'       => $user_id,
            'interessent'   => array_merge($user_int, $user_int2)  
        ]);

    }

    /**
     * @Route("/interessent", name="interessent_list")
     */
    public function interessentList(UserDao $uDao){

        $stmt = $uDao->getAllInteressent();

        $rows = array();
        while ($row = $stmt->fetchAssociative()) {    
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
     * @Route("/interessent/{uid}/delete", name="interessent_delete", requirements={"uid"="\d+"})
     */
    public function interessentDelete($uid, Request $request, UserDao $uDao)
    {
        $user_id = $uid;

        if ($request->isMethod('POST')) {

            if ($request->request->get('savebutton')) {
                   
                $em = $uDao->getEm();
                $em->getConnection()->beginTransaction();
                try {
		            $uDao->deleteInteressent([
			            'user_id' => $user_id
		            ]);

		            $em->getConnection()->commit();     
                } catch (\Exception $e) {
                    $em->getConnection()->rollBack();
                    throw $e;
                }
            }

            return $this->redirectToRoute('interessent_delete_list', [
                //'paramName' => 'value'
            ]);
        }

        $user_int = $uDao->getInteressent([
            'user_id' => $user_id
        ]);

        $user_int2 = $uDao->getUserAccount([
            'user_id' => $user_id
        ]);

        return $this->render('interessent/del.html.twig', [
            'user_id'      => $user_id,
            'interessent'  => array_merge($user_int, $user_int2)
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

    /**
     * @Route("/interessent/delete", name="interessent_delete_list")
     */
    public function interessentDelList(UserDao $uDao){

        $stmt = $uDao->getDelInteressent();
    
        $rows = array();
        while ($row = $stmt->fetchAssociative()) {        
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

}