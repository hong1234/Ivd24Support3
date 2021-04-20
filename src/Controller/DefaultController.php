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
    public function testPage(BaseDao $bDao)
    {
        $geschaeftsstelle_id = 3;

        $row = $bDao->getRowInTableByIdentifier('user_geschaeftsstelle', [
            'geschaeftsstelle_id' => $geschaeftsstelle_id
        ]);

        $bilderserver_id = $row['bilderserver_id'];
        $ftp_server_id   = $row['ftp_server_id'];
        $move_robot_id   = $row['move_robot_id'];

        return $this->render('default/test.html.twig', [
            //'sql' => $sql,
            'bilderserver_id' => $bilderserver_id,
            'ftp_server_id' => $ftp_server_id,
            'move_robot_id' => $move_robot_id
        ]);
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