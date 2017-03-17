<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Init;

use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * AbstractCommand
 *
 * @category Command
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  2013-03-20 18:05
 */
abstract class AbstractCommand extends Console\Command\Command
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    abstract public function verify(InputInterface $input, OutputInterface $output);

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

    /**
     * @todo
     */
    protected function revert()
    {
    }
}
