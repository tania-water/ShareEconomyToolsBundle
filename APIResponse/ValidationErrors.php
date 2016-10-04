<?php

namespace Ibtikar\ShareEconomyToolsBundle\APIResponse;

use Symfony\Component\Validator\Constraints as Assert;

class ValidationErrors extends MainResponse
{

    /**
     * @Assert\Type(type="boolean")
     */
    public $status = false;

    /**
     * @Assert\Type(type="integer")
     */
    public $code = 422;

    /**
     * @Assert\Type(type="string")
     */
    public $message = 'You have a validation error.';

    /**
     * @Assert\Type(type="array")
     */
    public $errors = array();

}