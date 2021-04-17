<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
// use App\Repository\ProductRepository;
//use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use App\Dao\MaklerDao;
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

    /**
     * @Route("/test", name="default_test")
     */
    public function testPage()
    {
        $email = 'myemail@yahoo.de';
        $passwort = 'abc123';
        return $this->render('default/test2.html.twig', [
            'email' => $email,
            'passwort' => $passwort
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