<?php

namespace Ibtikar\ShareEconomyToolsBundle\APIResponse;

class InvalidAPIKey extends AccessDenied
{

    /**
     * @param string $message
     */
    public function __construct($message = 'Invalid api key')
    {
        parent::__construct($message);
    }
}
