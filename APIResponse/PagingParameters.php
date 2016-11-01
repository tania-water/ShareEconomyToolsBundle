<?php

namespace Ibtikar\ShareEconomyToolsBundle\APIResponse;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Karim Shendy <kareem.elshendy@ibtikar.net.sa>
 */
class PagingParameters
{
    /**
     * @Assert\Type("integer")
     */
    public $maxResult = 5;

    /**
     * @Assert\Type("integer")
     */
    public $page = 1;

}
