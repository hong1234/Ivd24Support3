<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

//use App\Service\StringFormat;
//use App\Service\SendQueue;
// use App\Service\MaklerService;
use App\Service\StatisticService;

/**
 *
 * @Route(path="")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default_index")
     */
    public function indexPage()
    {
        // redirects to the "homepage" route
        // return $this->redirectToRoute('default_home');
        return $this->redirectToRoute('statistic_dashboard'); 
    }
    
    /**
     * @Route("/home", name="default_home")
     */
    public function homePage()
    {
        return $this->render('default/home.html.twig', [
        ]);
    }

    /**
     * @Route("/test", name="default_test")
     */
    public function testPage(StatisticService $stService)
    {
        $rs = $stService->objectRequestLast12Months();

        var_dump($rs); exit;
        // return $this->render('default/test.html.twig', [
        //     'bildpfad' => $bildpfad,
        //     'ftppfad' => $ftppfad,
        //     'bildserver' => $bildserver,
        //     'ftpserver' => $ftpserver
        // ]);
    }

}