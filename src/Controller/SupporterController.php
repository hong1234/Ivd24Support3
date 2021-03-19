<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
// use App\Repository\ProductRepository;

use App\Dao\UserDao;
use App\Service\SupporterService;

/**
 *
 * @Route(path="")
 */
class SupporterController extends AbstractController
{
    /**
     * @Route("/supporter", name="supporter_list")
     */
    public function supporterList(UserDao $uDao)
    {
        $stmt = $uDao->getAllSupportUser();

        $rows = array();
        while ($row = $stmt->fetchAssociative()) {
            $row2 = array();
            $row2[] = $row['user_id'];
            $row2[] = $row['username'];
            $row2[] = $row['email'];
            $row2[] = date("Y-m-d", (int)$row['registrierungsdatum']);
            $row2[] = date("Y-m-d", (int)$row['lastlogin']);
            $row2[] = "<a href=".$this->generateUrl('supporter_edit', array('uid' => $row['user_id'])).">Daten bearbeiten</a>";
            $row2[] = "<a href=".$this->generateUrl('supporter_delete', array('uid' => $row['user_id'])).">Supporter löschen</a>";
            
            $rows[] = $row2;
        }

        return $this->render('supporter/list.html.twig', [
            'dataSet' => $rows
        ]);
    }

    /**
     * @Route("/supporter/new", name="supporter_new")
     */
    public function newSupporter(Request $request,  SupporterService $supser)
    {
        $username = '';
        $email    = '';
        $passwort = '';

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            // post parameters
            //$safePost = filter_input_array(INPUT_POST);
            //var_dump($safePost); exit;

            $safePost = $request->request;
            $username  =  $safePost->get('username');
            $email     =  $safePost->get('email');
            $passwort  =  $safePost->get('passwort');
            
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                // $manager = $this->getDoctrine()->getManager();
                $supser->newSupporter($username, $email, $passwort);
                return $this->redirectToRoute('supporter_list', [
                    //'paramName' => 'value'
                ]);
            } 
            else {
                $email = $email."<--Invalid email format";
            }

        }

        return $this->render('supporter/new.html.twig', [
            'username' => $username,
            'email'    => $email,
            'passwort' => $passwort
        ]);
    }

    /**
     * @Route("/supporter/{uid}/edit", name="supporter_edit", requirements={"uid"="\d+"})
     */
    public function supporterEdit($uid, Request $request, UserDao $uDao, SupporterService $supser)
    {
        $user_id  = $uid;
        $username = '';
        $email    = '';
        $passwort = '';

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            // post parameters
            //$safePost = filter_input_array(INPUT_POST);
            //var_dump($safePost); exit;

            $safePost = $request->request;
            $username = $safePost->get('username');
            $email    = $safePost->get('email');
            $passwort = $safePost->get('passwort');
            
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

                $supser->updateSupporter($user_id, $username, $email, $passwort);
                return $this->redirectToRoute('supporter_list', [
                    //'paramName' => 'value'
                ]);    
            }
            else {
                $email = $email."<--Invalid email format";
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
            'user_id'    => $user_id,
            'username'   => $username,
            'email'      => $email,
            'passwort'   => $passwort
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
            'user_id'    => $user_id,
            'username'   => $suser['username'],
            'email'      => $suser['email']
        ]);

    }

}