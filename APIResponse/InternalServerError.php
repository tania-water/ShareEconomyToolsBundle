<?php

namespace Ibtikar\ShareEconomyToolsBundle\APIResponse;

use Symfony\Component\Validator\Constraints as Assert;

class InternalServerError
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
     * @Assert\Type(type="string")
     */
    public $message = 'Something is not right, Please contact the development team.';

}