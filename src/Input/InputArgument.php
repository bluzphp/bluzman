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

class InputArgument extends \Symfony\Component\Console\Input\InputArgument
{
    use InputValidationTrait;
}