<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
// use App\Repository\ProductRepository;
//use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use App\Dao\MaklerDao;
use App\Dao\BaseDao;
use App\Service\StringFormat;
use App\Service\SendQueue;
use App\Service\MaklerService;

/**
 *
 * @Route(path="")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/home", name="default_home")
     */
    public function homePage()
    {
        return $this->render('default/home.html.twig', [
        ]);
    }

    public function getRowInTableById(string $tabName, iterable $values=[]) {
        foreach($values as $key => $value) {
            $sql = "SELECT * FROM ".$tabName." WHERE $key = $value";
        }
        //return $this->doQuery($sql, $values)->fetch();
        return $sql;
    }

    /**
     * @Route("/test", name="default_test")
     */
    public function testPage(MaklerService $mService)
    {
        $rs = $mService->userMaklerConfig(1, 10137);
        var_dump($rs); exit;


        // $makler_config = $mDao->getRowInTableByIdentifier('user_makler_config', [
        //     'user_id' => 10346
        // ]);
    
        // $bildpfad   = $makler_config['bilderordner'];     // string 'b00111561' (length=9)
	    // $ftppfad    = $makler_config['ftp_benutzer'];     // string 'f00111561' (length=9)
        // //$username   = $makler_config['ftp_benutzer'];
        // $bildserver = $makler_config['bilderserver_id'];  // string '3' (length=1)
	    // $ftpserver  = $makler_config['ftp_server_id'];    // string '5' (length=1)

        // return $this->render('default/test.html.twig', [
        //     'bildpfad' => $bildpfad,
        //     'ftppfad' => $ftppfad,
        //     'bildserver' => $bildserver,
        //     'ftpserver' => $ftpserver
        // ]);
    }

    /**
     * @Route("/random", name="default_random")
     */
    public function randomPage(StringFormat $sfService)
    {
        $rs = $sfService->rand_str(8, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789&%!#@');
        return $this->render('default/test2.html.twig', [
            'random' => $rs,
        ]);
    }

    /**
     * @Route("/confirm", name="default_confirm")
     */
    public function confirmPage(SendQueue $sqSer)
    {
        $username = "TestUser";
        $email = "hong66.lenguyen@gmail.com";
        $passwort = "ABC123";
        $sqSer->addToSendQueue('makler_new', [
            'username' => $username,
            'email'    => $email, 
            'passwort' => $passwort
        ]);

        return $this->render( 
            'default/test2.html.twig', [
                'email' => $email,
                'passwort' => $passwort
            ]
        );
    }

}