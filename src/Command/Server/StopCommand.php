<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Server;

use Bluzman\Command\AbstractCommand;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Process\Process;

/**
 * StopCommand
 *
 * @category Command
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  2013-06-17 14:47
 */
class StopCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected $name = 'server:stop';

    /**
     * @var string
     */
    protected $description = 'Stop a built-in PHP server.';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var $config \Bluzman\Application\Config
         */
        $config = $this->getApplication()->getConfig()->server;

        $this->getOutput()->writeln($this->info('Running "server:stop" command ...'));

        $statusCommand = new StatusCommand();
        $statusCommand->setApplication($this->getApplication());
        $statusCommand->checkIsRunning();

        $process = new Process('kill -9 ' . $config['pid']);
        $process->run();

        $this->getApplication()->getConfig()->unsetOption('server');
    }
}