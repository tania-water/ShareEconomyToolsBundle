<?php

namespace Ibtikar\ShareEconomyToolsBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * @author Mahmoud Mostafa <mahmoud.mostafa@ibtikar.net.sa>
 */
class LanguageListener
{

    /**
     * @param GetResponseEvent $event
     */
    public function onRequest(GetResponseEvent $event)
    {
        $supportedLanguages = array('ar', 'en');
        $request = $event->getRequest();
        $requestedLocale = $request->get('lang');
        if ($requestedLocale && in_array($requestedLocale, $supportedLanguages)) {
            $request->setLocale($requestedLocale);
        }
    }
}
