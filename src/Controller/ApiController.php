<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use Twig\Environment;
use App\Service\StringFormat;

use App\Dao\GeoDao;
use App\Dao\MaklerDao;
use App\Dao\StockDao;
use App\Dao\StatisticDao;

/**
 *
 * @Route(path="")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/maklerdata", name="api_makler_data")
     */
    public function maklerData(Request $request, Environment $twig, MaklerDao $mDao, StockDao $stockDao, StatisticDao $sDao)
    {
        $maklerId = $request->request->get('maklerId', '');

        $makler = $mDao->getRowInTableByIdentifier('user_makler', [
            'user_id' => $maklerId
        ]);

        $bcUser = $mDao->getRowInTableByIdentifier('businessClubUser', [
            'user_id' => $maklerId
        ]);

        if($bcUser != null){
            $BusinessClub = 'Ja';
        } else {
            $BusinessClub = 'Nein';
        }

        $stock = $stockDao->getAktienAnzahlByUserId([
            'user_id' => $maklerId
        ]);

        $ftp = $mDao->getRowInTableByIdentifier('user_makler_config', [
            'user_id' => $maklerId
        ]);

        $date = new \DateTime();
        $date->modify('-4 week');

        $expose = $sDao->getExposeLast4WeekByUserId([
            'user_id' => $maklerId,
            'timepoint' => $date->format('Y-m-d H:i:s')
        ]);

        $frage = $sDao->getRequestLast4WeekByUserId([
            'user_id' => $maklerId,
            'timepoint' => $date->format('Y-m-d H:i:s')
        ]);

        $object = $sDao->getActivObjectAnzahlByUserId([
            'user_id' => $maklerId
        ]);

        $data = $twig->render('makler/more.html.twig', [
            'user_id' => $maklerId,
            'makler' => $makler,
            'BcClub' => $BusinessClub,
            'stock'  => $stock,
            'ftp'    => $ftp,
            'expose' => $expose,
            'request' => $frage,
            'object' => $object
        ]);
        
        return $this->json([
            'data' => $data
        ]);
    }
    
    /**
     * @Route("/seourl", name="api_seourl")
     */
    public function getSeoUrl(Request $request, StringFormat $fmService)
    {
        //$inputString = "Muster Immobilien Invest Firma GmbH & Co. KG in München";
        $firmaInputString = $request->request->get('firma', '');

        $rs = ['status'=> 'ok'];
        if(trim($firmaInputString) == ''){
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
            if ($geo_bundesland !== false){
                $rs['bundesland_id'] = $geo_bundesland['geo_bundesland_id'];
                $rs['geschaeftsstelle_id'] = $geo_bundesland['geschaeftsstelle_id']; 
            } else {
                $rs['status'] = 'notfound';
            }
        }
        return $this->json($rs);
    }

}