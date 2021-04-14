<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\HttpFoundation\Request;
// use App\Repository\ProductRepository;
//use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

//use App\Dao\ServerDao;
//use App\Service\SendQueue;

/**
 *
 * @Route(path="")
 */
class DefaultController extends AbstractController
{
     /**
     * @Route("/confirm", name="default_confirm")
     */
    public function confirmPage()
    {
        // return $this->json([
        //     'message' => 'Welcome to your new controller!',
        //     'path' => 'src/Controller/DefaultController.php',
        // ]);

        return $this->render('default/confirm.html.twig', [
        ]);
    }

    /**
     * @Route("/home", name="default_home")
     */
    public function homePage()
    {
        // return $this->json([
        //     'message' => 'Welcome to your new controller!',
        //     'path' => 'src/Controller/DefaultController.php',
        // ]);

        return $this->render('default/home.html.twig', [
        ]);
    }

    /**
     * @Route("/test", name="default_test")
     */
    public function testPage()
    {
        // return $this->json([
        //     'message' => 'Welcome to your new controller!',
        //     'path' => 'src/Controller/DefaultController.php',
        // ]);

        return $this->render('default/test.html.twig', [
        ]);
    }

}