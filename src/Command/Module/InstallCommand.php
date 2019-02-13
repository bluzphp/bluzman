<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Module;

use Bluzman\Command\AbstractCommand;
use Composer\Console\Application as ComposerApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class List command
 *
 * @package Bluzman\Command
 *
 * @author   Pavel Machekhin
 * @created  2013-03-28 14:03
 */
class InstallCommand extends AbstractCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/bluzman")
            ->setName('module:install')
            // the short description shown while running "php bin/bluzman list"
            ->setDescription('Install new module')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Install module, for retrieve list run command <info>bluzman module:list</info>')
        ;

        $module = new InputArgument(
            'module',
            InputArgument::REQUIRED,
            'Module name is required'
        );

        $this->getDefinition()->addArgument($module);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Composer\Factory::getHomeDir() method
        // needs COMPOSER_HOME environment variable set
        // putenv('COMPOSER_HOME=' . PATH_VENDOR . '/bin/composer');

        $arguments = [
            'command' => 'require',
            'packages' => ['bluzphp/module-' . $input->getArgument('module')]
        ];

        // call `composer install` command programmatically
        $composerInput = new ArrayInput($arguments);
        $application = new ComposerApplication();
        $application->setAutoExit(false); // prevent `$application->run` method from exiting the script
        $application->run($composerInput);
    }
}
