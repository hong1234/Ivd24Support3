<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Service\TemplateService;
use App\Dao\TemplateDao;

/**
 *
 * @Route(path="/support")
 */
class MailTemplateController extends AbstractController
{
    private $tempDao;
    private $templateService;
    
    public function __construct(TemplateDao $tempDao, TemplateService $templateService)
    {
        $this->tempDao = $tempDao;
        $this->templateService = $templateService;
    }

    /**
     * @Route("/template/list", name="template_list")
     */
    public function templateList()
    {
        $rows = $this->templateService->templatesByCategory2();
        return $this->render('temp/list.html.twig', [
            'dataSet' => $rows
        ]);
    }

    /**
     * @Route("/template/new", name="template_new")
     */
    public function templateNew(Request $request)
    {
        $templatename = '';
        $template = '';
        $dokument = '';

        $error = '';

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            
            //post parameters
            $safePost = $request->request;
            // var_dump($safePost); var_dump($_FILES['dokument']); exit;

            $templatename = $safePost->get('templatename');
            $template = $safePost->get('template');

            //validation
            // $error = $validator->isValidStatisticUserInput($safePost);
            if(preg_replace('/\s+/', '', $templatename) == ''){
                $error = $error."Feld 'templatename' darf nicht leer sein ---";
            }

            if(preg_replace('/\s+/', '', $template) == ''){
                $error = $error."Feld 'template' darf nicht leer sein ---";
            }

            if ($error == '') {
                if(isset($_FILES['dokument'])) {
                    if ($_FILES['dokument']['error'] == 0) {
                        // $path = $this->getParameter('kernel.project_dir').'/public/dokumente/'.basename($_FILES['dokument']['name']);
                        // $path = '/var/www/html/ivd24SupportTool/public/dokumente/'.basename($_FILES['dokument']['name']);
                        $path = '/var/www/html/dokumente/ivd24/'.basename($_FILES['dokument']['name']);
        
                        if(move_uploaded_file($_FILES['dokument']['tmp_name'], $path)) {
                            // $dokument = $path;
                            $dokument = basename($_FILES['dokument']['name']);
                        } else{
                            $error = $error."There was an error uploading the file ---";
                        }
                    }
                }
            }
            
            if ($error == '') {
                $this->templateService->insertTemplate($templatename, $template, $dokument);
                return $this->redirectToRoute('template_list', [
                    //  'paramName' => 'value'
                ]);
            } 

        }

        return $this->render('temp/new.html.twig', [ 
            'templatename' => $templatename,
            'template'     => $template,
            // 'dokument'     => $dokument,
            'error'        => $error,
            'briefanrede'  => '{{briefanrede}}',
            'anrede'       => '{{anrede}}',
            'vorname'      => '{{vorname}}',
            'nachname'     => '{{nachname}}',
            'user_id'      => '{{user_id}}'
        ]);

    }

     /**
     * @Route("/template/{tempid}/edit", name="template_edit", requirements={"tempid"="\d+"})
     */
    public function templateEdit($tempid, Request $request)
    {
        $templatename = '';
        $template = '';
        $dokument1 = '';
        $error = '';

        if ($request->isMethod('POST') && $request->request->get('savebutton')) {
            //post parameters
            $safePost = $request->request;
            // var_dump( $safePost); exit;

            $templatename = $safePost->get('templatename');
            $template  = $safePost->get('template');
            $dokument1 = $safePost->get('dokument1');

            //validation
            // $error = $validator->isValidStatisticUserInput($safePost);
            if(preg_replace('/\s+/', '', $templatename) == ''){
                $error = $error."Feld 'templatename' darf nicht leer sein ---";
            }

            if(preg_replace('/\s+/', '', $template) == ''){
                $error = $error."Feld 'template' darf nicht leer sein ---";
            }

            if ($error == '') {
                if(isset($_FILES['dokument'])) {
                    if ($_FILES['dokument']['error'] == 0) {
                        // $path = $this->getParameter('kernel.project_dir').'/public/dokumente/'.basename($_FILES['dokument']['name']);
                        // $path = '/var/www/html/ivd24SupportTool'.'/public/dokumente/'.basename($_FILES['dokument']['name']);
                        $path = '/var/www/html/dokumente/ivd24/'.basename($_FILES['dokument']['name']);
        
                        if(move_uploaded_file($_FILES['dokument']['tmp_name'], $path)) {
                            if(strlen($dokument1)>0){
                                unlink('/var/www/html/dokumente/ivd24/'.$dokument1);
                            }
                            // $dokument1 = $path;
                            $dokument1 = basename($_FILES['dokument']['name']);
                        } else{
                            $error = $error."There was an error uploading the file ---";
                        }
                    } else {
                        if(strlen($dokument1)>0){
                            unlink('/var/www/html/dokumente/ivd24/'.$dokument1);
                        }
                        $dokument1 = '';
                    }
                } 
            }

            if ($error == '') {
                $this->templateService->updateTemplate($tempid, $templatename, $template, $dokument1);
                return $this->redirectToRoute('template_list', [
                    //  'paramName' => 'value'
                ]);
            }

        }

        if ($request->isMethod('GET')) {
            $temp = $this->templateService->getTemplateById($tempid);
            $templatename = $temp->titel;
            $template = $temp->nachricht;
            $dokument1 = $temp->document_path;
        }

        return $this->render('temp/edit.html.twig', [
            'template_id' => $tempid,
            'templatename'=> $templatename,
            'template'    => $template,
            'dokument1'   => $dokument1,
            'error'       => $error,
            'briefanrede' => '{{briefanrede}}',
            'anrede'      => '{{anrede}}',
            'vorname'     => '{{vorname}}',
            'nachname'    => '{{nachname}}',
            'user_id'     => '{{user_id}}'
        ]);
    }

    /**
     * @Route("/template/{tempid}/delete", name="template_delete", requirements={"tempid"="\d+"})
     */
    public function templateDelete($tempid)
    {
        $this->tempDao->deleteTemplate([
            'template_id' => $tempid
        ]);

        return $this->redirectToRoute('template_list', [
            //  'paramName' => 'value'
        ]);
    }

}