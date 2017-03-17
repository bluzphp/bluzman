<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Server;

use Bluzman\Command\AbstractCommand;
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
class StatusCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected $name = 'server:status';

    /**
     * @var string
     */
    protected $description = 'Get the status of built-in PHP server.';

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

        try {
            $this->checkIsRunning();
            $this->getOutput()->writeln($this->info('Server is running at ' . $config['address']));
        } catch (NotRunningException $e) {
            $this->getOutput()->writeln($this->info('Server is not running.'));
        }
    }

    /**
     * @return bool
     * @throws NotRunningException
     */
    public function checkIsRunning()
    {
        /**
         * @var $config \Bluzman\Application\Config
         */
        $config = $this->getApplication()->getConfig()->server;

        if (!isset($config['pid'])) {
            throw new NotRunningException;
        }

        $process = new Process('ps -p ' . $config['pid'] . ' -o comm=');
        $process->run();

        if (is_null($process->getOutput())) {
            throw new NotRunningException;
        }

        return true;
    }
}
