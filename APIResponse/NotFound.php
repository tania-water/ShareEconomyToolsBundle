<?php

namespace Ibtikar\ShareEconomyToolsBundle\APIResponse;

use Symfony\Component\Validator\Constraints as Assert;

class NotFound extends MainResponse
{

    /**
     * @Assert\Type(type="boolean")
     */
    public $status = false;

    /**
     * @Assert\Type(type="integer")
     */
    public $code = 404;

    /**
     * @Assert\Type(type="string")
     */
    public $message;

    /**
     * @param string $message
     */
    public function __construct($message = 'Not found.')
    {
        $this->message = $message;
    }
}
