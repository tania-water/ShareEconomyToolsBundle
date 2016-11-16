<?php

namespace Ibtikar\ShareEconomyToolsBundle\APIResponse;

class Success extends MainResponse
{
    /**
     * @param string $message
     */
    public function __construct($message = 'Success.')
    {
        $this->message = $message;
    }

}