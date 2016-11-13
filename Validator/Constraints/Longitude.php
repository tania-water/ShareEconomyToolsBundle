<?php

namespace Ibtikar\ShareEconomyToolsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Regex;

/**
 * @Annotation
 */
class Longitude extends Regex
{
    public $message = 'Not a valid longitude';
    public $pattern = "/^(\+|-)?(?:180(?:(?:\.0{1,7})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,7})?))$/";

    public function validatedBy()
    {
        return 'Symfony\Component\Validator\Constraints\RegexValidator';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return [];
    }
}