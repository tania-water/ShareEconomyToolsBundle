<?php

namespace Ibtikar\ShareEconomyToolsBundle\APIResponse;

use Symfony\Component\Validator\Constraints as Assert;

class Success
{

    /**
     * @Assert\Type(type="boolean")
     */
    public $status = true;

    /**
     * @Assert\Type(type="integer")
     */
    public $code = 200;

    /**
     * @Assert\Type(type="string")
     */
    public $message = 'Success.';

}