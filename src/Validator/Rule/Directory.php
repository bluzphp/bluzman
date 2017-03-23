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
class Directory extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $template = '{{name}} must be a directory';

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
        if (is_string($input)) {
            return is_dir($input);
        } elseif ($input instanceof \SplFileInfo) {
            return $input->isDir();
        } else {
            return false;
        }
    }
}
