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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Class PhinxCommand
 *
 * @package Bluzman\Command
 *
 * @author   Pavel Machekhin
 * @created  6/18/14 9:43 AM
 */

class PhinxInitCommand extends AbstractCommand
{
    const PHINX_VERSION = '~0.3';

    /**
     * @var string
     */
    protected $name = 'phinx:init';

    /**
     * @var string
     */
    protected $description = 'Initialize phinx for application';

    /**
     * Require phinx as composer package
     *
     * @todo Use composer commands to require
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $process = new Process(
            'php ' . PATH_ROOT . '/vendor/bin/composer require robmorgan/phinx:' . self::PHINX_VERSION,
            $this->getApplication()->getWorkingPath()
        );

        while($process instanceof Process) {
            if (!$process->isStarted()) {
                $process->start();

                continue;
            }

            if (!$process->isRunning() || $process->isTerminated()) {
                $process = false;
            }

            // a little bit of workaround
            sleep(1);
        }

        $fs = new Filesystem();

        try {
            $fs->mkdir($this->getApplication()->getWorkingPath() . DS . 'migrations');
        } catch (IOExceptionInterface $e) {
            echo "An error occurred while creating your directory at ".$e->getPath();
        }

        $this->getOutput()->writeln($this->info('Phinx has been initialized.'));
    }
}
