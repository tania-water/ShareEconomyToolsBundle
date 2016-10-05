<?php

namespace Ibtikar\ShareEconomyToolsBundle\APIResponse;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Karim Shendy <kareem.elshendy@ibtikar.net.sa>
 */
class ItemsList extends MainResponse
{
    /**
     * @Assert\NotBlank
     */
    public $items = [];

}