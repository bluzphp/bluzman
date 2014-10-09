<?php

/**
 * NotRunningException
 *
 * @category Command
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  5/24/13 7:23 PM
 */

namespace Bluzman\Command\Server;

class NotRunningException extends \Exception
{
    protected $message = 'Server is not running';
}