<?php

namespace Bluzman\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * TestCommand
 *
 * !!!TODO
 *
 * @category Command
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  3/28/13 2:03 PM
 */

class TestCommand extends Console\Command\Command
{
    protected function configure()
    {
        $this
            ->setName('test')
            ->setDescription('Run tests');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $text = 'Running tests...';

        //do init
        $output->writeln($text);

        $this->getApplication()->callForContribute($output);
    }
}