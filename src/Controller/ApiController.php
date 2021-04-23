<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
// use App\Repository\ProductRepository;
//use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

//use App\Dao\MaklerDao;
use App\Dao\GeoDao;
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
        $firmaInputString = $request->request->get('firma', '');

        $rs = ['status'=> 'ok'];
        if($firmaInputString == ''){
            $rs['status'] = 'empty';
        } else {
            $rs['seo_url'] = $fmService->getSeoUrl($firmaInputString); 
        }
        return $this->json($rs);
    }

    /**
     * @Route("/geschaeftsstelle", name="api_geschaeftsstelle")
     */
    public function getGeschaeftsstelle(Request $request, GeoDao $geoDao)
    {
        //$plz = $inputString = '01468';
        $plzInputString = $request->request->get('plz', '');
        $plzInputString = trim(preg_replace('/\s+/', ' ', $plzInputString));
        $plzInputString = preg_replace('/ /', '', $plzInputString);

        $rs = ['status'=> 'ok'];
        if($plzInputString == ''){
            $rs['status'] = 'empty';
        } else {
            $geo_bundesland = $geoDao->getGeschaeftsstelleByPLZ($plzInputString);
            $rs['bundesland_id'] = $geo_bundesland['geo_bundesland_id'];
            $rs['geschaeftsstelle_id'] = $geo_bundesland['geschaeftsstelle_id']; 
        }
        return $this->json($rs);
    }

}