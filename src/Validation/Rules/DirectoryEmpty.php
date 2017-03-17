<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Validation\Rules;

use Symfony\Component\Finder\Finder;

/**
 * @package Bluzman\Validation\Rules
 */
class DirectoryEmpty extends \Respect\Validation\Rules\AbstractRule
{
    public function directoryEmpty($input)
    {
        return $this->validate($input);
    }

    public function validate($input)
    {
        $finder = new Finder();

        $itemsCount = $finder->in($input)->count();

        return $itemsCount < 1;
    }
}

