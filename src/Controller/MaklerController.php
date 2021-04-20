<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
// use App\Repository\ProductRepository;

use App\Dao\UserDao;
use App\Dao\MaklerDao;

use App\Service\MaklerService;
use App\Service\UserAccount;
use App\Service\StringFormat;

/**
 *
 * @Route(path="/support")
 */
class MaklerController extends AbstractController
{
    /**
     * @Route("/makler/new", name="makler_new")
     */
    public function newMakler(Request $request, MaklerDao $mDao, UserAccount $accSer, MaklerService $mService, StringFormat $sfService)
    {
        $mitgliedsnummer = '';
        $vorname = '';
        $name    = '';
        $firma   = '';
        $strasse = '';
        $plz     = '';
        $ort     = '';
        $bundesland_id = '';
        $telefon = '';
        $telefax = '';
        $homepage = '';
        $seo_url = '';
        $geschaeftsstelle_id = '';
        $username = '';
        $email    = '';
        $passwort = '';
        $ftppasswort = $sfService->rand_str(8, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789&%!#@');
        $error    = '';

        $bundeslaender    = $mDao->getBundeslaender();
        $geschaeftsstelle = $mDao->getAllGeschaeftsstelle();

        //if(isset($_POST['savebutton'])) { // savebutton: true
        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            //$safePost = filter_input_array(INPUT_POST);// post parameters
            $safePost = $request->request;

            $mitgliedsnummer = $safePost->get('mnummer');
            $vorname  = $safePost->get('vorname');
            $name     = $safePost->get('name');
            $firma    = $safePost->get('firma');
            $strasse  = $safePost->get('strasse');
            $plz      = $safePost->get('plz');
            $ort      = $safePost->get('ort');
            $bundesland_id = $safePost->get('bundesland');
            $telefon  = $safePost->get('telefon');
            $telefax  = $safePost->get('telefax');
            $homepage = $safePost->get('homepage');
	        
            $geschaeftsstelle_id = $safePost->get('geschaeftsstelle');

            $username  = $safePost->get('username');  
            $email     = $safePost->get('email');    
            $passwort  = $safePost->get('userpasswort');
            $seo_url   = $safePost->get('seo_url');
            $ftppasswort = $safePost->get('ftppasswort');

            //validation
            $error = $accSer->isValidMaklerData($username, $email, $passwort, $seo_url);

            if ($error == '') {
                $mService->newMakler($safePost);
                return $this->redirectToRoute('makler_list', [
                    //'paramName' => 'value'
                ]);
            }

        }
        
        return $this->render('makler/new.html.twig', [
            'mitgliedsnummer' => $mitgliedsnummer,
            'vorname' => $vorname,
            'name'    => $name,
            'firma'   => $firma,
            'strasse' => $strasse,
            'plz'     => $plz,
            'ort'     => $ort,
            'bundesland_id' => $bundesland_id,
            'telefon' => $telefon,
            'telefax' => $telefax,
            'homepage' => $homepage,
            'seo_url' => $seo_url,
            'geschaeftsstelle_id' => $geschaeftsstelle_id,

            'bundeslaender' => $bundeslaender,
            'geschaeftsstelle' => $geschaeftsstelle,

            'username' => $username,
            'email'    => $email,
            'passwort' => $passwort,
            'ftppasswort' => $ftppasswort,
            'error'    => $error  
        ]);

    }

    /**
     * @Route("/makler/{uid}/edit", name="makler_edit", requirements={"uid"="\d+"})
     */
    public function maklerEdit($uid, Request $request, MaklerDao $mDao, MaklerService $mService)
    {
        $user_id = $uid;
        // if(isset($_POST['savebutton'])) { // savebutton: true
        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            //$safePost = filter_input_array(INPUT_POST);
            //var_dump($safePost); exit;
            $safePost = $request->request;
            $mService->maklerEdit($user_id, $safePost);
            return $this->redirectToRoute('makler_list', [
                //'paramName' => 'value'
            ]);
        }
        
        $user_makler  = $mDao->getMakler([
            'user_id' => $user_id
        ]);
    
        $user_makler2  = $mDao->getUserAccount([
            'user_id' => $user_id
        ]);
        
        return $this->render('makler/edit.html.twig', [
            'user_id'  => $user_id,
            'makler'   => array_merge($user_makler, $user_makler2)
        ]);

    }

    /**
     * @Route("/makler/{uid}/lock/{gesperrt}", name="makler_lock_unlock")
     */
    public function maklerLock($uid, $gesperrt, UserDao $uDao){
        $user_id  = $uid;
        $uDao->updateUserAccountGesperrt([
            'gesperrt' => $gesperrt,
            'user_id'  => $user_id
        ]);

        return $this->redirectToRoute('makler_list', [
            //'paramName' => 'value'
        ]);
    }

    /**
     * @Route("/makler/{uid}/ftpedit", name="makler_ftp_edit", requirements={"uid"="\d+"})
     */
    public function maklerFtpPwEdit($uid, Request $request, MaklerDao $mDao, MaklerService $mService)
    {
        $user_id = $uid;
        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            // $safePost = filter_input_array(INPUT_POST);
            $safePost = $request->request;
            $mService->maklerFtpPwEdit($user_id, $safePost);
        }

        $makler_config = $mDao->getMaklerConfig([
            'user_id' => $user_id
        ]);

        $username = $makler_config['ftp_benutzer'];

        return $this->render('makler/ftpedit.html.twig', [
            'user_id'  => $user_id,
            'username' => $username
        ]);
    }

    /**
     * @Route("/makler/{uid}/pwedit", name="makler_pw_edit", requirements={"uid"="\d+"})
     */
    public function maklerPwEdit($uid, Request $request, UserDao $uDao, MaklerService $mService)
    {
        $user_id = $uid;

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            $safePost = $request->request;
            $mService->maklerPwEdit($user_id, $safePost);
        }

        $user_makler = $uDao->getUserAccount([
            'user_id' => $user_id
        ]);
        $username = $user_makler['username'];

        return $this->render('makler/pwedit.html.twig', [
            'user_id'    => $user_id,
            'username'   => $username
        ]);
    }

    /**
     * @Route("/makler", name="makler_list")
     */
    public function maklerList(MaklerService $mService)
    {
        $rows = $mService->MaklerList();
        return $this->render('makler/list.html.twig', [
            'dataSet' => $rows
        ]);
    }

    /**
     * @Route("/makler/delete", name="makler_delete_list")
     */
    public function maklerDelList(MaklerService $mService)
    {
        $rows = $mService->MaklerDelList();
        return $this->render('makler/del.list.html.twig', [
            'dataSet' => $rows
        ]);
    }

    /**
     * @Route("/makler/{uid}/intodelete", name="makler_into_delete", requirements={"uid"="\d+"})
     */
    public function maklerIntoDelList($uid, MaklerDao $mDao)
    {
        $user_id = $uid;

        $mDao->updateUserMaklerForDelete([
            'user_id' => $user_id
        ]);

        return $this->redirectToRoute('makler_list', [
            //'paramName' => 'value'
        ]);

    }

    /**
     * @Route("/makler/{uid}/delete", name="makler_delete", requirements={"uid"="\d+"})
     */
    public function maklerDelete($uid, Request $request, MaklerDao $mDao, MaklerService $mService)
    {
        $user_id = $uid;

        if ($request->isMethod('POST')) {

            if ($request->request->get('savebutton')) {
                $mService->deleteMakler($user_id);
            }
            return $this->redirectToRoute('makler_delete_list', [
                //'paramName' => 'value'
            ]);  
        }

        $user_makler  = $mDao->getMakler([
            'user_id' => $user_id
        ]);
    
        $user_makler2  = $mDao->getUserAccount([
            'user_id' => $user_id
        ]); 
        
        return $this->render('makler/del.html.twig', [
            'user_id'     => $user_id,
            'makler' => array_merge($user_makler, $user_makler2)
        ]);

    }

    /**
     * @Route("/makler/{uid}/delete/undo", name="makler_delete_undo", requirements={"uid"="\d+"})
     */
    public function deleteUndo($uid, UserDao $uDao){
        $user_id = $uid;
        $uDao->updateUserAccountLoeschung([
            'user_id' => $user_id
        ]);

        return $this->redirectToRoute('makler_delete_list', [
            //'paramName' => 'value'
        ]);
    }

}