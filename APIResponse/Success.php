<?php

namespace Ibtikar\ShareEconomyToolsBundle\APIResponse;

use Symfony\Component\Validator\Constraints as Assert;

class Success extends MainResponse
{
    /**
     * @Assert\Type(type="string")
     */
    public $message = 'Success.';

}