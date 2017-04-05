<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Db;

use Bluzman\Input\InputArgument;

/**
 * Run seed command
 *
 * @package  Bluzman\Command\Db
 * @author   Anton Shevchuk
 */
class SeedRunCommand extends AbstractDbCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/bluzman")
            ->setName('db:seed:run')
            // the short description shown while running "php bin/bluzman list"
            ->setDescription('Run seed file(s)')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command is shorthand to phinx tool')
        ;

        $name = new InputArgument('--seed', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Seed name(s) to run');
        $this->getDefinition()->addArgument($name);
    }
}
