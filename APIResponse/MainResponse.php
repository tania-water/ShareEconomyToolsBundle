<?php

namespace Ibtikar\ShareEconomyToolsBundle\APIResponse;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of MainResponse
 *
 * @author Karim Shendy <kareem.elshendy@ibtikar.net.sa>
 */
class MainResponse
{
    /**
     * @Assert\Type("integer")
     */
    public $code = 200;

    /**
     * @Assert\Type("boolean")
     */
    public $status = true;

    /**
     * @Assert\Type("string")
     */
    public $message = '';

}