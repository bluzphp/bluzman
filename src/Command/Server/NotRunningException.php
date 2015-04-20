<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

/**
 * NotRunningException
 *
 * @category Command
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  2013-06-17 15:11
 */

namespace Bluzman\Command\Server;

class NotRunningException extends \Exception
{
    protected $message = 'Server is not running';
}