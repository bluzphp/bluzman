<?php

/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Server;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * ServerCommand
 *
 * @package  Bluzman\Command\Server
 *
 * @author   Pavel Machekhin
 * @created  2013-05-24 19:23
 */
class StartCommand extends AbstractServerCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/bluzman")
            ->setName('server:start')
            // the short description shown while running "php bin/bluzman list"
            ->setDescription('Launche a built-in PHP server')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to start built-in PHP server')
        ;

        $this->addOption('host', null, InputOption::VALUE_OPTIONAL, 'IP address of the server', '0.0.0.0');
        $this->addOption('port', null, InputOption::VALUE_OPTIONAL, 'Port of the server', '8000');
        $this->addOption('background', 'b', InputOption::VALUE_NONE, 'Run the server in the background');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->info('Running "server:start" command ... [' . $input->getOption('env') . ']');

        $host = $input->getOption('host');
        $port = $input->getOption('port');
        $env = $input->getOption('env');

        // setup BLUZ_ENV to environment
        // enable BLUZ_DEBUG
        // use public/routing.php
        $process = Process::fromShellCommandline(
            "export BLUZ_ENV=$env && export BLUZ_DEBUG=1 && export BLUZ_LOG=1 && php -S $host:$port routing.php",
            $this->getApplication()->getWorkingPath() . DS . 'public'
        );

        $this->write("Server has been started at <info>$host:$port</info>");

        if ($input->getOption('background')) {
            $process->disableOutput();
            $process->start();

            $processId = $this->getProcessId($input->getOption('host'), $input->getOption('port'));

            $this->write("PID is <info>$processId</info>");
        } else {
            while ($process instanceof Process) {
                if (!$process->isStarted()) {
                    $process->start();
                    continue;
                }

                echo $process->getIncrementalOutput();
                echo $process->getIncrementalErrorOutput();

                if (!$process->isRunning() || $process->isTerminated()) {
                    $process = false;

                    $this->info('Server has been stopped.');
                }

                sleep(1);
            }
        }
        return 0;
    }
}
