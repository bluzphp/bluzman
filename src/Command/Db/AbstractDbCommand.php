<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Db;

use Bluzman\Command\AbstractCommand;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * AbstractDbCommand
 *
 * @package  Bluzman\Command\Db
 * @author   Anton Shevchuk
 */
abstract class AbstractDbCommand extends AbstractCommand
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandName = substr($this->getName(), 3);
        $arguments = $this->getDefinition()->getArguments();

        $phinx = new PhinxApplication();
        $command = $phinx->find($commandName);

        $phinxArguments = [];

        foreach ($arguments as $name => $argumentInput) {
            if ($input->getArgument($name)) {
                $phinxArguments[$name] = $input->getArgument($name);
            }
        }

        $phinxArguments['command'] = $commandName;
        $phinxArguments['--configuration'] = PATH_APPLICATION . DS . 'configs' . DS . 'phinx.php';
        $phinxArguments['--environment'] = 'default';

        $phinxInput = new ArrayInput($phinxArguments);
        $command->run($phinxInput, $output);
    }
}
