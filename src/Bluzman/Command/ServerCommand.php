<?php

namespace Bluzman\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * ServerCommand
 *
 * @category Command
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  5/24/13 9:23 PM
 */

class ServerCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected $name = 'server';

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
        return array(
            array('host', InputArgument::OPTIONAL, 'IP address of the server', '127.0.0.1'),
            array('port', InputArgument::OPTIONAL, 'Port of the server', '1337'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = [];

        if ($this->systemIsCorrect()) {
            $options[] = ['background', 'b', InputOption::VALUE_NONE, 'Run the server in the background'];
        }

        return $options;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //get application config
        $config = $this->getApplication()->getConfig();

        $this->setHost($this->getInput()->getArgument('host'));
        $this->setPort($this->getInput()->getArgument('port'));
        $this->setEnvironment($this->getInput()->getOption('env'));

        $this->getOutput()->writeln($this->info('Running "server" command at ' . $this->getAddress() . ' [' . $this->getEnvironment() . '].'));

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

    protected function systemIsCorrect()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN';
    }

    protected function getBaseCommand()
    {
        return 'export BLUZ_ENV=' . $this->getEnvironment() . ' && php -S ' . $this->getAddress();
    }

    /**
     * @return string
     */
    protected function getProcessId($pattern)
    {
        $this->showProgress();

        // dirty hack - need to wait for the process creation
        sleep(1);

        return trim(shell_exec('ps aux | grep "'.$pattern.'" | awk \'{print $2}\' | sed -n \'2p\''));
    }

    /**
     * @param $address
     * @param $environment
     */
    protected function startServer()
    {
        $publicDir = $this->getApplication()->getWorkingPath() . DS . 'public';

        chdir($publicDir);

        $shellCommand = $this->getBaseCommand();

        if ($this->getInput()->getOption('background') && $this->systemIsCorrect()) {
            $shellCommand .= ' > /dev/null &';
            pclose(popen($shellCommand, 'r'));

            $processId = $this->getProcessId($this->getAddress());

            $this->getOutput()->writeln($this->info('Server has been started in the background. Process ID: #' . $processId));
        } else {
            shell_exec($shellCommand);
        }
    }
}
