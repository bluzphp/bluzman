<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Validator\Rule;

use Bluz\Validator\Rule\AbstractRule;

/**
 * @package Bluzman\Validation\Rules
 */
class Writable extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $template = '{{name}} must be writable';

    /**
     * @param mixed $input
     * @return bool
     */
    public function validate($input) : bool
    {
        if ($input instanceof \SplFileInfo) {
            return $input->isWritable();
        }
        return is_string($input) && is_writable($input);
    }
}
