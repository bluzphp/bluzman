<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Init;

use Bluzman\Input;
use Bluzman\Command;
use Bluzman\Validation\Rules\DirectoryEmpty;
use Respect;
use Respect\Validation\Validator as v;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Process\Process;

/**
 * AllCommand
 *
 * @category Command
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  2013-03-20 17:45
 */

class AllCommand extends Command\AbstractCommand
{
    /**
     * @var string
     */
    protected $name = 'init:all';

    /**
     * @var string
     */
    protected $description = 'Initialize and scaffold a new project';

    protected $cmdPattern;

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
        return [
            [
                'name',
                null,
                InputOption::VALUE_OPTIONAL,
                'Name of new project',
                null,
                v::alnum('_-')->noWhitespace()
            ],
            [
                'path',
                null,
                InputOption::VALUE_OPTIONAL,
                'Project root path',
                null,
                v::directory()->writable()
            ]
        ];
    }

    public function __construct($name = null)
    {
        parent::__construct($name);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \RuntimeException
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->info('Running "init:all" command'));

        $this->cloneProject()
            ->generateConfig();

        // verify the skeleton was clone and config was created
        $this->verify($input, $output);

        $output->writeln('Project ' . $this->info($this->getOption('name') . ' has been successfully initialized.'));
    }

    /**
     * @return $this
     * @throws \Bluzman\Input\InputException
     */
    protected function cloneProject()
    {
        $name = $this->getOption('name');
        $path = $this->getOption('path');

        $this->getOutput()->writeln('Cloning skeleton project...');

        chdir($path);

        $projectPath = realpath($path) . DS . $name;

        if (is_dir($projectPath)) {
            $validator = new DirectoryEmpty();
            if (!$validator->validate($projectPath)) {
                throw new Input\InputException('"' . $projectPath . '" must be empty.');
            }
        }

        //@todo Use symfony process
        // create skeleton project
        $process = new Process(sprintf($this->getCmdPattern(), $name));
        $process->setTimeout(1600);
        $process->run();
        chdir($projectPath);

        return $this;
    }

    protected function validate()
    {

    }

    /**
     * @return string
     */
    protected function getCmdPattern()
    {
        return 'php ' . PATH_VENDOR . DS . 'bin' . DS
            . 'composer create-project bluzphp/skeleton %s'
            . ' --stability=dev --no-dev --keep-vcs --verbose';
    }

    /**
     * @return $this
     */
    protected function generateConfig()
    {
        $name = $this->getOption('name');

        $bluzmanDirectory = $this->getApplication()->getBluzmanPath();

        if (!is_dir($bluzmanDirectory)) {
            mkdir($bluzmanDirectory);
        }

        $this->getOutput()->writeln('Generating bluzman configuration file...');

        chdir($bluzmanDirectory . DS . '..');

        // generate bluzman config
        $this->getApplication()->getConfig()->putOptions([
            'name' => $name,
            'version' => $this->getApplication()->getVersion()
        ]);

        return $this;
    }

    /**
     * @return bool
     * @throws \RuntimeException
     */
    public function verify()
    {
        if (!$this->getApplication()->getConfig()) {
            throw new \RuntimeException('Something is wrong with project path. Unable to verify config.');
        }

        return true;
    }
}
