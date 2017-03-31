<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TestCommand
 *
 * @package Bluzman\Command
 *
 * @author   Pavel Machekhin
 * @created  2013-03-28 14:03
 */
class MagicCommand extends AbstractCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/bluzman")
            ->setName('i-need-magic')
            // the short description shown while running "php bin/bluzman list"
            ->setDescription('Magic is here')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to contribute')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->callForContribute();
    }
}
