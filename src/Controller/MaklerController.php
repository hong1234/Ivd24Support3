<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Service\MaklerService;
use App\Service\StringFormat;
use App\Validator\UserAccount;
use App\Dao\MaklerDao;

/**
 *
 * @Route(path="/support")
 */
class MaklerController extends AbstractController
{
    private $mDao;
    private $mService;
    
    public function __construct(MaklerDao $mDao, MaklerService $mService)
    {
        $this->mDao = $mDao;
        $this->mService = $mService;
    }

    /**
     * @Route("/makler/new", name="makler_new")
     */
    public function maklerNew(Request $request, UserAccount $validator, StringFormat $sfService)
    {
        $error = '';
        //if(isset($_POST['savebutton'])) { // savebutton: true
        if ($request->isMethod('POST') && $request->request->get('savebutton')) {

            //$safePost = filter_input_array(INPUT_POST);// post parameters as array
            //$postArray = $request->request->all();// post parameters as array
            $safePost = $request->request;

            //validation
            $error = $validator->isValidMaklerInput($safePost);

            if ($error == '') {
                $this->mService->newMakler($safePost);
                return $this->redirectToRoute('makler_list', [
                    //'paramName' => 'value'
                ]);
            }

            $makler = $safePost->all(); // post parameters as array
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
                'telefon' => '',
                'telefax' => '',
                'homepage' => '',
                'seo_url' => '',
                'email'   => '', 
                'username' => '',
                'passwort' => $sfService->getRandPassWord(),
                'ftppasswort' => $sfService->getRandPassWord()
            ]; 
        }

        $bundeslaender = $this->mDao->getAllRowsInTable('geo_bundesland');
        $geschaeftsstelle = $this->mDao->getAllRowsInTable('user_geschaeftsstelle');
        $mkategorien = array(
            ['mkategorie_id' => 'OM', 'mkname' => 'Ordentliches Mitglied'],
            ['mkategorie_id' => 'ZM', 'mkname' => 'Zweitmitglied'],
            ['mkategorie_id' => 'Ex1', 'mkname' => 'Existenzgründer im 1. Jahr'],
            ['mkategorie_id' => 'Ex2', 'mkname' => 'Existenzgründer im 2. Jahr'],
            ['mkategorie_id' => 'EM', 'mkname' => 'Ehrenmitglieder']
        );
        
        return $this->render('makler/new.html.twig', [
            'geschaeftsstelle' => $geschaeftsstelle,
            'bundeslaender' => $bundeslaender,
            'mkategorien' => $mkategorien,
            'makler' => $makler,
            'error' => $error  
        ]);

    }

    /**
     * @Route("/makler/{uid}/edit", name="makler_edit", requirements={"uid"="\d+"})
     */
    public function maklerEdit($uid, Request $request, UserAccount $validator)
    {
        $user_id = $uid;
        $error = '';

        // if(isset($_POST['savebutton'])) { // savebutton: true
        if ($request->isMethod('POST') && $request->request->get('savebutton')) {

            //$safePost = filter_input_array(INPUT_POST);// post parameters
            //$postArray = $request->request->all();// post parameters as array
            $safePost = $request->request;

            //validation
            $error = $validator->isValidMaklerInputByUpdate($user_id, $safePost);

            if ($error == '') {
                $this->mService->maklerEdit($user_id, $safePost);
                return $this->redirectToRoute('makler_list', [
                    //'paramName' => 'value'
                ]);
            }

            $makler = $safePost->all(); // post parameters as array
        }

        if ($request->isMethod('GET')) {
            $makler = $this->mService->getMaklerData($user_id);
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
    public function maklerDelete($uid, Request $request)
    {
        $user_id = $uid;
        if ($request->isMethod('POST')) {

            if ($request->request->get('savebutton')) {
                $this->mService->deleteMakler($user_id);
            }
            return $this->redirectToRoute('makler_delete_list', [
                //'paramName' => 'value'
            ]);  
        }

        $user_makler = $this->mService->getMaklerData($user_id);
        
        return $this->render('makler/del.html.twig', [
            'user_id' => $user_id,
            'makler' => $user_makler
        ]);

    }
    
    /**
     * @Route("/makler/{uid}/lock", name="makler_lock")
     */
    public function maklerLock($uid)
    {
        $user_id  = $uid;
        $this->mDao->updateUserAccountGesperrt([
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
    public function maklerUnLock($uid)
    {
        $user_id  = $uid;
        $this->mDao->updateUserAccountGesperrt([
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
    public function maklerFtpPwEdit($uid, Request $request)
    {
        $user_id = $uid;
        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            // $safePost = filter_input_array(INPUT_POST);
            $safePost = $request->request;
            $this->mService->maklerFtpPwEdit($user_id, $safePost);
            return $this->redirectToRoute('makler_list', []);
        }

        $makler_config = $this->mDao->getRowInTableByIdentifier('user_makler_config', [
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
    public function maklerPwEdit($uid, Request $request)
    {
        $user_id = $uid;
        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            $safePost = $request->request;
            $this->mService->maklerPwEdit($user_id, $safePost);
            return $this->redirectToRoute('makler_list', []);
        }

        $user_makler = $this->mDao->getRowInTableByIdentifier('user_account', [
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
    public function maklerList()
    {
        $rows = $this->mService->MaklerList();
        return $this->render('makler/list.html.twig', [
            'dataSet' => $rows
        ]);
    }

    /**
     * @Route("/makler/delete", name="makler_delete_list")
     */
    public function maklerDelList()
    {
        $rows = $this->mService->MaklerDelList();
        return $this->render('makler/del.list.html.twig', [
            'dataSet' => $rows
        ]);
    }

    /**
     * @Route("/makler/{uid}/intodelete", name="makler_into_delete", requirements={"uid"="\d+"})
     */
    public function maklerIntoDelList($uid)
    {
        $user_id = $uid;
        $this->mDao->updateUserMaklerForDelete([
            'user_id' => $user_id
        ]);

        return $this->redirectToRoute('makler_list', [
            //'paramName' => 'value'
        ]);

    }

    /**
     * @Route("/makler/{uid}/delete/undo", name="makler_delete_undo", requirements={"uid"="\d+"})
     */
    public function deleteUndo($uid) 
    {
        $user_id = $uid;
        $this->mDao->updateUserAccountLoeschung([
            'user_id' => $user_id
        ]);

        return $this->redirectToRoute('makler_delete_list', [
            //'paramName' => 'value'
        ]);
    }

}