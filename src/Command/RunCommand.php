<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command;

use Application\CliBootstrap;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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

        $params = new InputArgument(
            'uri',
            InputArgument::REQUIRED,
            'URI to call'
        );

        $this->getDefinition()->addArgument($params);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = CliBootstrap::getInstance();
        $app->setInput($input);
        $app->setOutput($output);
        $app->init($input->getOption('env'));
        $app->run();
    }
}
