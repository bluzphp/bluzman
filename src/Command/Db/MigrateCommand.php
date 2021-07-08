<?php

/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Db;

/**
 * Run migrate command
 *
 * @package  Bluzman\Command\Db
 * @author   Anton Shevchuk
 */
class MigrateCommand extends AbstractDbCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/bluzman")
            ->setName('db:migrate')
            // the short description shown while running "php bin/bluzman list"
            ->setDescription('Apply DB migrations')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command is shorthand to phinx tool')
        ;
    }
}
