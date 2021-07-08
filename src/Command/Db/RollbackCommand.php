<?php

/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Db;

use Bluzman\Input\InputArgument;

/**
 * Run rollback command
 *
 * @package  Bluzman\Command\Db
 * @author   Anton Shevchuk
 */
class RollbackCommand extends AbstractDbCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/bluzman")
            ->setName('db:rollback')
            // the short description shown while running "php bin/bluzman list"
            ->setDescription('Rollback last migration')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command is shorthand to phinx tool')
        ;
    }
}
