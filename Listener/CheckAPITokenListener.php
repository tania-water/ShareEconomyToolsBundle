<?php

namespace Ibtikar\ShareEconomyToolsBundle\Listener;

use Ibtikar\ShareEconomyToolsBundle\Service\APIOperations;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * @author Mahmoud Mostafa <mahmoud.mostafa@ibtikar.net.sa>
 */
class CheckAPITokenListener
{

    /** @var $apiKeys array */
    private $apiKeys = array();

    /** @var APIOperations $apiOperations */
    private $apiOperations;

    /**
     * @param APIOperations $apiOperations
     */
    public function __construct(APIOperations $apiOperations, $androidAPIKey, $iosAPIKey)
    {
        $this->apiOperations = $apiOperations;
        $this->apiKeys['android'] = $androidAPIKey;
        $this->apiKeys['ios'] = $iosAPIKey;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (strpos($request->getRequestUri(), '/api/doc') === false && strpos($request->getRequestUri(), '/api') !== false) {
            $apiKeyIndex = array_search($request->headers->get('X-Api-Key'), $this->apiKeys);
            if ($apiKeyIndex === false) {
                $event->setResponse($this->apiOperations->getInvalidAPIKeyJsonResponse());
                return;
            }
            $request->attributes->set('requestFrom', $apiKeyIndex);
        }
    }
}
