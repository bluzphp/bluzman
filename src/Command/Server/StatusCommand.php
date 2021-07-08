<?php

/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Server;

use Bluzman\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * StatusCommand
 *
 * @package  Bluzman\Command\Server
 *
 * @author   Pavel Machekhin
 * @created  2013-06-17 14:52
 */
class StatusCommand extends AbstractServerCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/bluzman")
            ->setName('server:status')
            // the short description shown while running "php bin/bluzman list"
            ->setDescription('Get the status of built-in PHP server')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to check built-in PHP server')
        ;
        $this->addOption('host', null, InputOption::VALUE_OPTIONAL, 'IP address of the server', '0.0.0.0');
        $this->addOption('port', null, InputOption::VALUE_OPTIONAL, 'Port of the server', '8000');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->info('Running "server:status" command');
        try {
            $host = $input->getOption('host');
            $port = $input->getOption('port');

            $pid = $this->getProcessId($host, $port) ?: false;

            if (!$pid) {
                throw new NotRunningException();
            }

            $process = new Process("ps -p $pid -o comm=");
            $process->run();

            $processOutput = $process->getOutput();

            if (empty($processOutput)) {
                throw new NotRunningException();
            }

            $this->write("Server <info>$host:$port</info> is running. PID is <info>$pid</info>");
        } catch (NotRunningException $e) {
            $this->comment('Server is not running');
        }
    }
}
