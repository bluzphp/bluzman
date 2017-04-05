<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Server;

/**
 * NotRunningException
 *
 * @package  Bluzman\Command\Server
 *
 * @author   Pavel Machekhin
 * @created  2013-06-17 15:11
 */
class NotRunningException extends \Exception
{
    protected $message = 'Server is not running';
}
