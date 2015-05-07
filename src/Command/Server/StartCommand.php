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
 * ServerCommand
 *
 * @category Command
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  2013-05-24 19:23
 */

class StartCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected $name = 'server:start';

    /**
     * @var string
     */
    protected $description = 'Launches a built-in PHP server.';

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $port;

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
        $options = [
            ['host', null, InputOption::VALUE_OPTIONAL, 'IP address of the server', '127.0.0.1'],
            ['port', null, InputOption::VALUE_OPTIONAL, 'Port of the server', '1337'],
            ['background', 'b', InputOption::VALUE_NONE, 'Run the server in the background']
        ];

        return $options;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setHost($this->getInput()->getOption('host'));
        $this->setPort($this->getInput()->getOption('port'));
        $this->setEnvironment($this->getInput()->getOption('env'));

        $this->getOutput()->writeln(
            $this->info('Running "server:start" command ... [' . $this->getEnvironment() . ']')
        );

        $this->startServer();
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param string $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @return string
     */
    protected function getAddress()
    {
        return $this->getHost() . ':' . $this->getPort();
    }

    protected function getBaseCommand()
    {
        return 'export BLUZ_ENV=' . $this->getEnvironment() . ' && php -S ' . $this->getAddress();
    }

    /**
     * @return string
     */
    protected function getProcessId()
    {
        $this->showProgress();

        $pattern = 'php -S ' . $this->getAddress();

        return trim(shell_exec('ps aux | grep "'.$pattern.'" | grep -v grep  | grep -v BLUZ_ENV | awk \'{print $2}\''));
    }

    /**
     * @param $address
     * @param $environment
     */
    protected function startServer()
    {
        $publicDir = $this->getApplication()->getWorkingPath() . DS . 'public';
        $shellCommand = $this->getBaseCommand();

        $process = new Process($shellCommand, $publicDir);

        if ($this->getInput()->getOption('background')) {
            $process->disableOutput();
            $process->start();

            $processId = $this->getProcessId();

            $this->getApplication()->getConfig()->setOption('server', [
                'pid' => $processId,
                'address' => $address = 'http://' . $this->getAddress()
            ]);

            $this->getOutput()->writeln($this->info('Server has been started at ' . $address));
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

                    $this->getOutput()->writeln("");
                    $this->getOutput()->writeln($this->info('Server has been stopped.'));
                }

                sleep(1);
            }
        }
    }
}
