<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

//use App\Service\StringFormat;
//use App\Service\SendQueue;
// use App\Service\MaklerService;
use App\Service\StatisticService;

use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Bundle\SnappyBundle\Snappy\Response\JpegResponse;
use Knp\Snappy\Pdf;
use Knp\Snappy\Image;

use App\Dao\GeoDao;


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

    /**
     * @Route("/geo", name="default_geo")
     */
    public function geoTest(GeoDao $geoDao)
    {
        $rs = $geoDao->getBundeslandByPLZobj([
            'plz' => 80636
        ]);

        var_dump($rs); 
        exit;
        // return $this->render('default/test.html.twig', [
        //     'bildpfad' => $bildpfad,
        //     'ftppfad' => $ftppfad,
        //     'bildserver' => $bildserver,
        //     'ftpserver' => $ftpserver
        // ]);
    }

    /**
     * @Route("/pdf", name="default_pdf")
     */
    public function pdfAction(Pdf $knpSnappyPdf)
    {
        // $html = $this->renderView('default/test2.html.twig', array(
        //     'random'  => 'Müller'
        // ));

        // echo $html; 
        $knpSnappyPdf->generate('http://localhost:8000/abc', 'file.pdf');
        exit;

        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html, array(
                //'page-size' => 'Letter',
                'images' => true,
                //'enable-javascript' => true,
                //'javascript-delay' => 5000
            )),
            'file.pdf'
        );
    }

    /**
     * @Route("/image", name="default_image")
     */
    public function imageAction(Image $knpSnappyImage)
    {
        $html = $this->renderView('default/test2.html.twig', array(
            'random'  => 'Müller'
        ));

        // echo $html; exit;

        return new JpegResponse(
            $knpSnappyImage->getOutputFromHtml($html),
            'image.jpg'
        );
    }

    /**
     * @Route("/distance", name="default_distance")
     */
    public function distanceTest(GeoDao $geoDao)
    {
        $rs = $geoDao->getMaklerByDistanceKm([
            'latitude' => '48.145067',
            'longitude' => '11.5605772'
        ]);

        var_dump($rs); 
        exit;
        // return $this->render('default/test.html.twig', [
        //     'bildpfad' => $bildpfad,
        //     'ftppfad' => $ftppfad,
        //     'bildserver' => $bildserver,
        //     'ftpserver' => $ftpserver
        // ]);
    }

    /**
     * @Route("/abc", name="default_abc")
     */
    public function abcAction(Pdf $knpSnappyPdf)
    {
        return $this->render('default/test2.html.twig', [
            'random'  => 1234
        ]);
    }

}