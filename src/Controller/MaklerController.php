<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
// use App\Repository\ProductRepository;

use App\Validator\UserAccount;

use App\Dao\UserDao;
use App\Dao\MaklerDao;

use App\Service\MaklerService;
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
    public function maklerNew(Request $request, MaklerDao $mDao, UserAccount $validator, MaklerService $mService, StringFormat $sfService)
    {
        $bundeslaender    = $mDao->getAllRowsInTable('geo_bundesland');
        $geschaeftsstelle = $mDao->getAllRowsInTable('user_geschaeftsstelle');
        $mkategorien = array(
            ['mkategorie_id' => 'OM', 'mkname' => 'Ordentliches Mitglied'],
            ['mkategorie_id' => 'ZM', 'mkname' => 'Zweitmitglied'],
            ['mkategorie_id' => 'Ex1', 'mkname' => 'Existenzgründer im 1. Jahr'],
            ['mkategorie_id' => 'Ex2', 'mkname' => 'Existenzgründer im 2. Jahr'],
            ['mkategorie_id' => 'EM', 'mkname' => 'Ehrenmitglieder']
        );
        $error = '';

        //if(isset($_POST['savebutton'])) { // savebutton: true
        if ($request->isMethod('POST') && $request->request->get('savebutton')) {

            //$safePost = filter_input_array(INPUT_POST);// post parameters as array
            //$safePost = $request->request->all();// post parameters as array
            //var_dump($safePost); exit;

            $safePost = $request->request;

            $username = $safePost->get('username');
            $email    = $safePost->get('email'); 
            $passwort = $safePost->get('passwort');
            $ftppasswort = $safePost->get('ftppasswort');
            $seo_url  = $safePost->get('seo_url');

            //validation
            $empty1 = $validator->isEmptyUsername($username);
            $empty2 = $validator->isEmptyEmail($email);
            $empty3 = $validator->isEmptyPasswort($passwort);
            $empty4 = $validator->isEmptyFtpPasswort($ftppasswort);
            $empty5 = $validator->isEmptySeoUrl($seo_url);
            $error1 = $empty1.$empty2.$empty3.$empty4.$empty5;

            $error2 = $validator->isValidUserName($username);
            $error3 = $validator->isValidEmail($email);
            $error4 = $validator->isValidSeoUrl($seo_url);
            $error  = $error1.$error2.$error3.$error4;
           
            if ($error == '') {
                $mService->newMakler($safePost);
                return $this->redirectToRoute('makler_list', [
                    //'paramName' => 'value'
                ]);
            }

            $makler = $safePost->all();// post parameters as array
        }

        if ($request->isMethod('GET')) {
            $makler = [
                'geschaeftsstelle_id' => '', 
                'bundesland_id' => '', 
                'mkategorie_id' => '', 
    
                'mitgliedsnummer' => '', 
                'firma'   => '',   
                'anrede'  => '', 
                'titel'   => '', 
                'vorname' => '',
                'name'    => '', 
                'strasse' => '',
                'plz'     => '',  
                'ort'     => '', 
    
                'telefon'  => '',
                'telefax'  => '',
                'homepage' => '',
                'seo_url'  => '',
    
                'email'    => '', 
                'username' => '',
                'passwort' => $sfService->getRandPassWord(),
                'ftppasswort' => $sfService->getRandPassWord()
            ]; 
        }
        
        return $this->render('makler/new.html.twig', [
            'geschaeftsstelle' => $geschaeftsstelle,
            'bundeslaender' => $bundeslaender,
            'mkategorien' => $mkategorien,
            'makler' => $makler,
            'error'  => $error  
        ]);

    }

    /**
     * @Route("/makler/{uid}/edit", name="makler_edit", requirements={"uid"="\d+"})
     */
    public function maklerEdit($uid, Request $request, UserAccount $validator, MaklerService $mService)
    {
        $user_id = $uid;
        $error = '';

        // if(isset($_POST['savebutton'])) { // savebutton: true
        if ($request->isMethod('POST') && $request->request->get('savebutton')) {

            //$safePost = filter_input_array(INPUT_POST);// post parameters
            //$safePost = $request->request->all();// post parameters as array
            //var_dump($safePost); exit;

            $safePost = $request->request;
            
            $email   = $safePost->get('email');
            $seo_url = $safePost->get('seo_url');

            //validation
            $empty1 = $validator->isEmptyEmail($email);
            $empty2 = $validator->isEmptySeoUrl($seo_url);
            $error1 = $empty1.$empty2;

            $error2 = $validator->isValidEmailByUpdate($user_id, $email);
            $error3 = $validator->isValidSeoUrlByUpdate($user_id, $seo_url);
            $error  = $error1.$error2.$error3;

            if ($error == '') {
                $mService->maklerEdit($user_id, $safePost);
                return $this->redirectToRoute('makler_list', [
                    //'paramName' => 'value'
                ]);
            }

            $makler = $safePost->all();// post parameters as array
        }

        if ($request->isMethod('GET')) {
            $makler = $mService->getMaklerData($user_id);
            $makler['telefax'] = $makler['fax'];
        }

        return $this->render('makler/edit.html.twig', [
            'user_id' => $user_id,
            'makler'  => $makler,
            'error'   => $error
        ]);

    }

    /**
     * @Route("/makler/{uid}/delete", name="makler_delete", requirements={"uid"="\d+"})
     */
    public function maklerDelete($uid, Request $request, MaklerService $mService)
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

        $user_makler = $mService->getMaklerData($user_id);
        
        return $this->render('makler/del.html.twig', [
            'user_id' => $user_id,
            'makler' => $user_makler
        ]);

    }
    
    /**
     * @Route("/makler/{uid}/lock", name="makler_lock")
     */
    public function maklerLock($uid, UserDao $uDao)
    {
        $user_id  = $uid;
        $uDao->updateUserAccountGesperrt([
            'gesperrt' => 1,
            'user_id'  => $user_id
        ]);

        return $this->redirectToRoute('makler_list', [
            //'paramName' => 'value'
        ]);
    }

    /**
     * @Route("/makler/{uid}/unlock", name="makler_unlock")
     */
    public function maklerUnLock($uid, UserDao $uDao)
    {
        $user_id  = $uid;
        $uDao->updateUserAccountGesperrt([
            'gesperrt' => 0,
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
            return $this->redirectToRoute('makler_list', []);
        }

        $makler_config = $mDao->getRowInTableByIdentifier('user_makler_config', [
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
            return $this->redirectToRoute('makler_list', []);
        }

        $user_makler = $uDao->getRowInTableByIdentifier('user_account', [
            'user_id' => $user_id
        ]);
        $username = $user_makler['username'];

        return $this->render('makler/pwedit.html.twig', [
            'user_id'  => $user_id,
            'username' => $username
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
     * @Route("/makler/{uid}/delete/undo", name="makler_delete_undo", requirements={"uid"="\d+"})
     */
    public function deleteUndo($uid, UserDao $uDao) 
    {
        $user_id = $uid;
        $uDao->updateUserAccountLoeschung([
            'user_id' => $user_id
        ]);

        return $this->redirectToRoute('makler_delete_list', [
            //'paramName' => 'value'
        ]);
    }

}