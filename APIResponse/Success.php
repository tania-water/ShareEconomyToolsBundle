<?php

namespace Ibtikar\ShareEconomyToolsBundle\APIResponse;

use Symfony\Component\Validator\Constraints as Assert;

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