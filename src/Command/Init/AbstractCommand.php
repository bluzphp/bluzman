<?php

namespace Bluzman\Command\Init;

use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * Command
 *
 * @category Command
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  3/20/13 6:05 PM
 */

abstract class AbstractCommand extends Console\Command\Command
{
    /**
     * @return mixed
     * @throws \RuntimeException
     */
    protected function getConfig()
    {
        if (is_null($this->getApplication())) {
            throw new \RuntimeException('Application does not initialized yet.');
        }

        return $this->getApplication()->getConfig();
    }

    abstract public function verify(InputInterface $input, OutputInterface $output);

    /**
     * @todo
     */
    protected function revert()
    {

    }
}
