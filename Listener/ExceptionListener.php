<?php

namespace Ibtikar\ShareEconomyToolsBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    protected $env;

    public function __construct($env)
    {
        $this->env = $env;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        switch ($this->env) {
            case "dev":
                $response = $event->getResponse();
                break;
            default:
                $error500 = new \Ibtikar\ShareEconomyToolsBundle\APIResponse\InternalServerError();
                $response = new \Symfony\Component\HttpFoundation\JsonResponse($error500);
                break;
        }
        $event->setResponse($response);
    }

}
