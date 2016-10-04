<?php

namespace Ibtikar\ShareEconomyToolsBundle\APIResponse;

use Symfony\Component\Validator\Constraints as Assert;

class InvalidAPIKey extends AccessDenied
{

    /**
     * @Assert\Type(type="string")
     */
    public $message = 'Invalid api key';

}