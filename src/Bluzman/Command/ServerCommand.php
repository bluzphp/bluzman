<?php

namespace Bluzman\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Command\Command;
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

class ServerCommand extends Console\Command\Command
{
    protected $host = '127.0.0.1';

    protected $port = '3501';

    protected function configure()
    {
        $this
            ->setName('server')
            ->setDescription('Launches a built-in PHP server.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            //@todo Add an ability to define address parameters.
            $config = $this->getApplication()->getConfig();

            $address = $this->getAddress();
            $env = $config->environment;

        } catch (\RuntimeException $e) {
            throw new \RuntimeException('Command must be runned from the root folder of application.');
        }

        $text = "Running server at " . $address . "...\n";

        //do init
        $output->writeln($text);

        $publicDirectory = $this->getApplication()->getPath() . DIRECTORY_SEPARATOR . 'public';

        // something weird =)
        if (!is_dir($publicDirectory)) {
            throw new \RuntimeException('Failed to find `public` directory.');
        }

        // @todo: BLUZ_ENV will be default anyway, because currently skeleton doesn't detect arguments from environment,
        //        so, it's need to be fixed there with separate pull-request
        exec('cd ' . $publicDirectory . ' && BLUZ_ENV=' . $env . ' php -S ' . $address);
    }

    /**
     * @return string
     */
    protected function getAddress()
    {
        return $this->host . ':' . $this->port;
    }
}