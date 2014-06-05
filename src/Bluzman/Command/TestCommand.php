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
 * Class TestCommand
 *
 * @package Bluzman\Command
 *
 * @author   Pavel Machekhin
 * @created  3/28/13 2:03 PM
 */

class TestCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected $name = 'test';

    /**
     * @var string
     */
    protected $description = 'Run application tests';

    /**
     * @todo
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->callForContribute();
    }
}