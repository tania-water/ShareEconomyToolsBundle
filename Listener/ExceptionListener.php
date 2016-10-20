<?php

namespace Ibtikar\ShareEconomyToolsBundle\Listener;

use Ibtikar\ShareEconomyToolsBundle\APIResponse as ToolsBundleAPIResponses;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExceptionListener
{

    private $env;

    public function __construct($env)
    {
        $this->env = $env;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();
        if (strpos($request->getRequestUri(), '/api/doc') === false && strpos($request->getRequestUri(), '/api') !== false) {
            if ($this->env !== 'dev') {
                $event->setResponse(new JsonResponse(new ToolsBundleAPIResponses\InternalServerError()));
                if ($event->getException() instanceof NotFoundHttpException) {
                    $event->setResponse(new JsonResponse(new ToolsBundleAPIResponses\NotFound()));
                }
            }
        }
    }
}
