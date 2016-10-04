<?php

namespace Ibtikar\ShareEconomyToolsBundle\APIResponse;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Karim Shendy <kareem.elshendy@ibtikar.net.sa>
 */
class PagingList extends MainResponse
{
    /**
     * @Assert\NotBlank
     */
    public $items = [];

    /**
     * @Assert\NotBlank
     */
    public $currentPage;

    /**
     * @Assert\NotBlank
     */
    public $hasNextPage;

}