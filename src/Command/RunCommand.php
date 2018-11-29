<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command;

use Application\CliBootstrap;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class List command
 *
 * @package Bluzman\Command
 *
 * @author   Pavel Machekhin
 * @created  2013-03-28 14:03
 */
class RunCommand extends AbstractCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/bluzman")
            ->setName('run')
            // the short description shown while running "php bin/bluzman list"
            ->setDescription('Run controller')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Run specified controller with options params')
        ;

        $uri = new InputArgument(
            'uri',
            InputArgument::REQUIRED,
            'URI to call'
        );

        $this->getDefinition()->addArgument($uri);


        $debug = new InputOption(
            '--debug',
            '-d',
            InputOption::VALUE_NONE,
            'Display debug information'
        );

        $this->getDefinition()->addOption($debug);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \Bluz\Application\Exception\ApplicationException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('debug')) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }
        $app = CliBootstrap::getInstance();
        $app->setInput($input);
        $app->setOutput($output);
        $app->init($input->getOption('env'));
        $app->run();
    }
}
