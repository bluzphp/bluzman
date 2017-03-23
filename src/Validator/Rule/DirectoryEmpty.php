<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Validator\Rule;

use Bluz\Validator\Rule\AbstractRule;
use Symfony\Component\Finder\Finder;

/**
 * @package Bluzman\Validation\Rules
 */
class DirectoryEmpty extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $template = '{{name}} must be an empty directory';

    /**
     * @param $input
     * @return bool
     */
    public function directoryEmpty($input)
    {
        return $this->validate($input);
    }

    /**
     * @param mixed $input
     * @return bool
     */
    public function validate($input) : bool
    {
        $finder = new Finder();

        $itemsCount = $finder->in($input)->count();

        return $itemsCount < 1;
    }
}
