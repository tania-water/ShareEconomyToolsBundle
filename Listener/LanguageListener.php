<?php

namespace Ibtikar\ShareEconomyToolsBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * @author Mahmoud Mostafa <mahmoud.mostafa@ibtikar.net.sa>
 */
class LanguageListener
{
    protected $acceptedLocales;

    public function __construct($acceptedLocales)
    {
        $this->acceptedLocales = $acceptedLocales;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onRequest(GetResponseEvent $event)
    {
        $request         = $event->getRequest();
        $requestedLocale = $request->headers->get('lang');

        if ($requestedLocale && in_array($requestedLocale, $this->acceptedLocales)) {
            $request->setLocale($requestedLocale);
        }
    }
}