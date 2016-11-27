<?php

namespace Ibtikar\ShareEconomyToolsBundle\Listener;

use Ibtikar\ShareEconomyToolsBundle\APIResponse as ToolsBundleAPIResponses;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExceptionListener
{

    /* @var $env string */
    private $env;

    /* @var $logger \Monolog\Logger */
    private $logger;

    /**
     * @param string $env
     * @param \Monolog\Logger $logger
     */
    public function __construct($env, $logger)
    {
        $this->env = $env;
        $this->logger = $logger;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();
        if (strpos($request->getRequestUri(), '/api/doc') === false && strpos($request->getRequestUri(), '/api') !== false) {
            if ($this->env !== 'dev') {
                $exception = $event->getException();
                $event->setResponse(new JsonResponse(new ToolsBundleAPIResponses\InternalServerError()));
                if ($exception instanceof NotFoundHttpException) {
                    $event->setResponse(new JsonResponse(new ToolsBundleAPIResponses\NotFound()));
                } elseif ($exception instanceof MethodNotAllowedHttpException) {
                    $event->setResponse(new JsonResponse(new ToolsBundleAPIResponses\MethodNotAllowed($exception->getMessage())));
                } else {
                    $this->logger->critical($exception->getMessage());
                    $this->logger->critical($exception->getTraceAsString());
                }
            }
        }
    }
}
