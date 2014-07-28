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
use Respect\Validation\Validator as v;

/**
 * Class PhinxCreateCommand
 *
 * @package Bluzman\Command
 *
 * @author   Pavel Machekhin
 * @created  6/18/14 7:12 PM
 */

class PhinxCreateCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected $name = 'phinx:create';

    /**
     * @var string
     */
    protected $description = 'Create a new migration';

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'name',
                null,
                InputOption::VALUE_REQUIRED,
                'The name of new migration.',
                null,
                v::alnum('_-')->noWhitespace()
            ]
        ];
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $phinxProvider = new \Bluzman\Migrations\Provider\Phinx(
            $this->getApplication(),
            $input->getOption('env')
        );

        $name = $this->getOption('name');

        $command = new \Phinx\Console\Command\Create();
        $command->run(
            new Console\Input\ArrayInput([
                'name' => $name,
                '-c' => $phinxProvider->getConfigPath()
            ]),
            $output
        );
    }
}