<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
// use App\Repository\ProductRepository;
//use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

//use App\Dao\MaklerDao;
use App\Service\StringFormat;

/**
 *
 * @Route(path="")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/seourl", name="api_seourl")
     */
    public function getSeoUrl(Request $request, StringFormat $fmService)
    {
        //$inputString = "Muster Immobilien Invest Firma GmbH & Co. KG in München";
        $inputString = $request->request->get('text', '');

        if($inputString == ''){
            $seoUrl = "Bitte geben Sie die Firma-Angaben zuerst";
        } else {
            $seoUrl = $fmService->getSeoUrl($inputString);
        }
        
        return $this->json([
            'seourl' => $seoUrl
        ]);
    }

}