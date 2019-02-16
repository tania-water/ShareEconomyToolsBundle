<?php

namespace Ibtikar\ShareEconomyToolsBundle\APIResponse;

use Symfony\Component\Validator\Constraints as Assert;

class InternalServerError extends MainResponse
{

    /**
     * @Assert\Type(type="boolean")
     */
    public $status = false;

    /**
     * @Assert\Type(type="integer")
     */
    public $code = 500;

    /**
     * @param string $message
     */
   //public function __construct($message = 'Something is not right, Please contact the development team.')
    public function __construct($message = 'Something is not right, Please check it and try again.')
    {
        $this->message = $message;
    }
}
