<?php

/**
 * @namespace
 */

namespace Bluzman\Command\Server;

use Bluzman\Command\AbstractCommand;

/**
 * AbstractServerCommand
 *
 * @package  Bluzman\Command\Server
 * @author   Anton Shevchuk
 */
abstract class AbstractServerCommand extends AbstractCommand
{
    /**
     * @param string $host
     * @param integer $port
     * @return string
     */
    protected function getProcessId(string $host, int $port)
    {
        return trim(
            shell_exec("ps aux | grep 'php -S $host:$port' | grep -v grep | grep -v BLUZ_ENV | awk '{print $2}'")
        );
    }
}
