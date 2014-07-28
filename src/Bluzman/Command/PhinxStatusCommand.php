<?php

namespace Bluzman\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Process\Process;

/**
 * Class PhinxStatusCommand
 *
 * @package Bluzman\Command
 *
 * @author   Pavel Machekhin
 * @created  6/18/14 7:12 AM
 */

class PhinxStatusCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected $name = 'phinx:status';

    /**
     * @var string
     */
    protected $description = 'Initialize phinx for application';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $formatter = $this->getHelperSet()->get('formatter');

        $phinxProvider = new \Bluzman\Migrations\Provider\Phinx(
            $this->getApplication(),
            $input->getOption('env')
        );

        $arguments = [
            '-c' => $phinxProvider->getConfigPath(),
            '-e' => $input->getOption('env')
        ];

        $command = new \Phinx\Console\Command\Status();
        $command->run(
            new Console\Input\ArrayInput($arguments),
            $output
        );

//        $process = new Process(
//            './vendor/bin/phinx status -c ' . $phinxProvider->getConfigPath(),
//            $this->getApplication()->getWorkingPath()
//        );
//        $process->run();
//
//        $output->writeln($process->getOutput());
//        $output->writeln($process->getErrorOutput());
    }
}