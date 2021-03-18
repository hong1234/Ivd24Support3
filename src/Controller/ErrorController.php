<?php
namespace App\Controller;

//use App\Controller\Base\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ErrorController extends AbstractController //extends BaseController
{

    public function show(FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        // return $this->getView('bundles/TwigBundle/Exception/error.html.twig', [
        //     "code" => $exception->getStatusCode(),
        //     "message" =>$exception->getStatusText()
        // ]);

        return $this->render('error/error.html.twig', [
            "code" => $exception->getStatusCode(),
            "message" =>$exception->getStatusText()
        ]);
    }
}