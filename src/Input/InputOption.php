<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Input;

use Respect\Validation;

/**
 * @package Bluzman\Input
 */

class InputOption extends \Symfony\Component\Console\Input\InputOption
{
    use InputValidationTrait;
}
