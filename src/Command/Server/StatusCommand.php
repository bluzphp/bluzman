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
 * @category Command
 * @package  Bluzman
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
        try {
            $pid = $this->getProcessId($input->getOption('host'), $input->getOption('port')) ?: false;

            if (!$pid) {
                throw new NotRunningException;
            }

            $process = new Process("ps -p $pid -o comm=");
            $process->run();

            $processOutput = $process->getOutput();

            if (empty($processOutput)) {
                throw new NotRunningException;
            }

            $this->info("Server is running. PID is $pid");
        } catch (NotRunningException $e) {
            $this->info('Server is not running');
        }
    }
}
