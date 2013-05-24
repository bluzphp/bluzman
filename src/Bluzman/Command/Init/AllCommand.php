<?php

namespace Bluzman\Command\Init;

use Bluzman\Command\Command;
use Bluzman\Generator\Template\AbstractTemplate;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\ProgressHelper;

/**
 * AllCommand
 *
 * @category Command
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  3/20/13 5:45 PM
 */

class AllCommand extends AbstractCommand
{
    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('init:all')
            ->setDescription('Initialize and scaffold a new project')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Name of new project'
            )
            ->addArgument(
                'path',
                InputArgument::OPTIONAL,
                'Project root path'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \RuntimeException
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->getConfig();

            // This means that config is exist in current directory, so there is another project.
            throw new \Exception('Unable to initialize new project in the root folder of another.');
        } catch (\RuntimeException $e) {
            // continue normally

        } catch (\Exception $e) {
            // stop script
            throw new \RuntimeException($e->getMessage());
        }

        $output->writeln('<info>Running "init:all" command</info>');

        $name = $input->getArgument('name');
        $path = $input->getArgument('path');

        // `name` is required argument
        if (empty($name)) {
            // command should be executed only in interactive mode
            if (!$input->isInteractive()) {
                throw new \RuntimeException('Command should be executed in interactive mode.');
            }

            $output->writeln('');
            $name = $this->askName($output);
        }

        $projectPath = realpath($path);

        if (!$projectPath) {
            throw new \RuntimeException('Directory "' . $path . '" is not exists.');
        }

        // check if project directory is writable
        if (!is_writable($projectPath)) {
            throw new \RuntimeException('Directory "' . $projectPath . '" is not writable.');
        }

        $output->writeln('');

        // create new folder in current working directory if user doesn't set own path
        if (empty($path)) {
            $projectPath = $projectPath . DIRECTORY_SEPARATOR . $name;
        }

        $environment = $this->askEnvironment($output);

        $output->writeln('');

        // create new project directory
        if (!is_dir($projectPath)) {
            mkdir($projectPath);
        } else {
            //check if directory not empty
            $empty = (($files = @scandir($projectPath)) && count($files) <= 2);

            if (!$empty) {
                throw new \RuntimeException('Directory "' . $projectPath . '" is not empty.');
            }
        }

        // change working directory
        chdir($projectPath);

        $output->write(['Cloning skeleton project...'], true);

        // clone bluzphp skeleton
        $this->cloneSkeleton($output);

        $output->writeln('Generating bluzman configuration file...');

        // generate bluzman config
        $this->getApplication()->generateConfig($name, $environment);

        // verify the skeleton was clone and config was created
        $this->verify($input, $output);

        $output->writeln('');

        $output->writeln('Project <info>"' . $name . '"</info> was successfully initialized.');
    }

    /**
     * @param OutputInterface $output
     * @return mixed
     */
    protected function askName(OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        // ask user enter a valid name of new project
        return $dialog->askAndValidate(
            $output,
            "<question>Please enter the name of the project:</question> \n> ",
            function ($name) use ($output, $dialog) {
                if (empty($name)) {
                    $output->writeln('<error>ERROR: Please enter a correct name of the project</error>');

                    return $this->askName($output);
                } else {
                    return $name;
                }
            },
            true
        );
    }

    /**
     * @param OutputInterface $output
     * @return mixed
     */
    protected function askEnvironment(OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        // ask user enter a valid name of new model
        $environment = $dialog->select(
            $output,
            '<question>Please choose your environment:</question> ',
            $this->getEnvironmentChoices()
        );

        return $environment;
    }

    /**
     * @return array
     */
    protected function getEnvironmentChoices()
    {
        return [
            \Bluzman\Bluzman::ENVIRONMENT_PRODUCTION => 'production',
            \Bluzman\Bluzman::ENVIRONMENT_DEVELOPMENT => 'development'
        ];
    }

    /**
     * @todo
     *
     * @return bool
     * @throws \RuntimeException
     */
    public function verify(InputInterface $input, OutputInterface $output)
    {
        if (!$this->getConfig()) {
            throw new \RuntimeException('Something is wrong with project path. Unable to verify config.');
        }

        return true;
    }

    /**
     * @param OutputInterface $output
     * @throws \RuntimeException
     */
    protected function cloneSkeleton(OutputInterface $output)
    {
        $cmd = $this->getApplication()->getComposerCmd();

        // install skeleton project
        shell_exec($cmd . ' create-project bluzphp/skeleton . --stability=dev --no-progress --keep-vcs');

        // clear VCS files related to skeleton project
        shell_exec('rm .git -rf');

        // verify that skeleton has been successfully cloned
        if (!is_readable($this->getApplication()->getPath() . DIRECTORY_SEPARATOR . 'composer.json')) {
            throw new \RuntimeException('Something went wrong. Project initialization failed. Please try again.');
        }
    }
}