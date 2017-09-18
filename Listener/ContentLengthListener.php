<?php

namespace Ibtikar\ShareEconomyToolsBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Description of ContentLengthListener
 *
 * @author Mahmoud Mostafa <mahmoud.mostafa@ibtikar.net.sa>
 */
class ContentLengthListener
{

    /**
     * @param FilterResponseEvent $event
     */
    public function onResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $headers = $response->headers;
        if (!$response->isRedirection() && !$headers->has('Content-Length') && !$headers->has('Transfer-Encoding') && !$response instanceof StreamedResponse) {
            $headers->add(array('Content-Length' => strlen($response->getContent())));
        }
    }
}
