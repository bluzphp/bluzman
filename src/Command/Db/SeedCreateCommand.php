<?php

/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Db;

use Bluzman\Input\InputArgument;

/**
 * Create seed command
 *
 * @package  Bluzman\Command\Db
 * @author   Anton Shevchuk
 */
class SeedCreateCommand extends AbstractDbCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/bluzman")
            ->setName('db:seed:create')
            // the short description shown while running "php bin/bluzman list"
            ->setDescription('Create seed file')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command is shorthand to phinx tool')
        ;

        $name = new InputArgument('name', InputArgument::REQUIRED, 'Seed name is required');

        $this->getDefinition()->addArgument($name);
    }
}
