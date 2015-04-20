<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Input;

/**
 * @package Bluzman\Input
 */

class InputException extends \Exception
{
    protected $message = 'Command must be runned in interactive mode';
}