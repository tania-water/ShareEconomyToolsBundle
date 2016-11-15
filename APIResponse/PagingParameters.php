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
     * @Assert\Range(min=5)
     */
    public $maxResult = 5;

    /**
     * @Assert\Type("integer")
     * @Assert\Range(min=1)
     */
    public $page = 1;

}
