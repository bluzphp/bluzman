<?php

/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command;

use Codeception;
use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class List command
 *
 * @package Bluzman\Command
 *
 * @author   Pavel Machekhin
 * @created  2013-03-28 14:03
 */
class TestCommand extends AbstractCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/bluzman")
            ->setName('test')
            // the short description shown while running "php bin/bluzman list"
            ->setDescription('Run tests')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Run codeception tests')
        ;

        $this->addModuleArgument(InputArgument::OPTIONAL);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // call `codeception run` command programmatically
        $arguments = [
            'run',
            '--config ' . PATH_ROOT . DS . 'codeception.yml'
        ];

        if ($group = $input->getArgument('module')) {
            $arguments[] = '--group ' . $group;
        }
        $codeceptionInput = new StringInput(implode(' ', $arguments));
        $command = $this->getCodeceptionApplication()->find('run');

        return $command->run($codeceptionInput, $output);
    }

    /**
     * Return CodeceptionApplication
     */
    protected function getCodeceptionApplication(): Codeception\Application
    {
        // @todo need refactoring this part - move functions to separate files
        require_once PATH_VENDOR . DS . 'codeception' . DS . 'codeception' . DS . 'autoload.php';

        $app = new Codeception\Application('Codeception', Codeception\Codecept::VERSION);
        $app->add(new Codeception\Command\Run('run'));

        $app->registerCustomCommands();

        return $app;
    }
}
