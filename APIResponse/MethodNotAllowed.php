<?php

namespace Ibtikar\ShareEconomyToolsBundle\APIResponse;

use Symfony\Component\Validator\Constraints as Assert;

class MethodNotAllowed extends MainResponse
{

    /**
     * @Assert\Type(type="boolean")
     */
    public $status = false;

    /**
     * @Assert\Type(type="integer")
     */
    public $code = 405;

    /**
     * @param string $message
     */
    public function __construct($message = 'Method not allowed.')
    {
        $this->message = $message;
    }
}
