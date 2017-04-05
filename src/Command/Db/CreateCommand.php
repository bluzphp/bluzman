<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Db;

use Bluzman\Input\InputArgument;

/**
 * Create migration command
 *
 * @package  Bluzman\Command\Db
 * @author   Anton Shevchuk
 */
class CreateCommand extends AbstractDbCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/bluzman")
            ->setName('db:create')
            // the short description shown while running "php bin/bluzman list"
            ->setDescription('Create DB migrations')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command is shorthand to phinx tool')
        ;

        $name = new InputArgument('name', InputArgument::REQUIRED, 'Migration name is required');

        $this->getDefinition()->addArgument($name);
    }
}
