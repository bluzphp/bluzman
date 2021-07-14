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
 * StopCommand
 *
 * @package  Bluzman\Command\Server
 *
 * @author   Pavel Machekhin
 * @created  2013-06-17 14:47
 */
class StopCommand extends AbstractServerCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/bluzman")
            ->setName('server:stop')
            // the short description shown while running "php bin/bluzman list"
            ->setDescription('Stop a built-in PHP server')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to stop built-in PHP server')
        ;
        $this->addOption('host', null, InputOption::VALUE_OPTIONAL, 'IP address of the server', '0.0.0.0');
        $this->addOption('port', null, InputOption::VALUE_OPTIONAL, 'Port of the server', '8000');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->info('Running "server:stop" command');

        $host = $input->getOption('host');
        $port = $input->getOption('port');

        $pid = $this->getProcessId($host, $port) ?: false;

        if (empty($pid)) {
            $this->comment('Server is not running');
            return 1;
        }

        $process = Process::fromShellCommandline("kill -9 $pid");
        $process->run();

        $this->write("Server <info>$host:$port</info> stopped");
        return 0;
    }
}
