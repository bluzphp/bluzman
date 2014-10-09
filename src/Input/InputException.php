<?php
/**
 * @author bashmach
 * @created 2014-01-04 00:40
 */

namespace Bluzman\Input;

class InputException extends \Exception
{
    protected $message = 'Command must be runned in interactive mode';
}