<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Generate;

use Bluzman\Generator\GeneratorException;
use Bluzman\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate Module Structure
 *
 * @package  Bluzman\Command
 */
class ModuleCommand extends AbstractGenerateCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/bluzman")
            ->setName('generate:module')
            // the short description shown while running "php bin/bluzman list"
            ->setDescription('Generate a new module')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to generate a module structure')
        ;

        $this->addModuleArgument();

        $controller = new InputArgument(
            'controller',
            InputArgument::OPTIONAL|InputArgument::IS_ARRAY,
            'Controller name(s)'
        );

        $this->getDefinition()->addArgument($controller);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->write("Running <info>generate:module</info> command");

            $module = $input->getArgument('module');

            $argument = $this->getDefinition()->getArgument('module');
            $argument->validate($module);

            // create main folder and subfolders
            $this->generate($input, $output);

            // verify it
            $this->verify($input, $output);

            $this->write("Module <info>$module</info> has been successfully created.");

            // create controllers
            $controllers = $input->getArgument('controller') ?? [];

            $command = $this->getApplication()->find('generate:controller');

            foreach ($controllers as $controller) {
                $arguments = [
                    'command' => 'generate:controller',
                    'module' => $module,
                    'controller' => $controller
                ];
                $greetInput = new ArrayInput($arguments);
                $command->run($greetInput, $output);
            }
        } catch (\Exception $e) {
            $this->error("ERROR: {$e->getMessage()}");
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function generate(InputInterface $input, OutputInterface $output)
    {
        $this->addSubFolders(
            $this->getApplication()->getModulePath($input->getArgument('module')),
            [
                'controllers',
                'views'
            ]
        );
    }

    /**
     * @param string $path
     * @param string[] $subFolders
     */
    protected function addSubFolders($path, array $subFolders = [])
    {
        if (!$this->getFs()->exists($path)) {
            $this->getFs()->mkdir($path);
        }

        foreach ($subFolders as $subFolderName) {
            $subFolderPath = $path . DIRECTORY_SEPARATOR . $subFolderName;
            if ($this->getFs()->exists($subFolderPath)) {
                $this->comment("Directory <info>$subFolderPath</info> already exists");
            } else {
                $this->getFs()->mkdir($subFolderPath, 0755);
                $this->getFs()->touch([$subFolderPath . DIRECTORY_SEPARATOR . '.keep']);
            }
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws GeneratorException
     */
    public function verify(InputInterface $input, OutputInterface $output)
    {
        $modulePath = $this->getApplication()->getModulePath($input->getArgument('module'));

        $paths = [
            $modulePath,
            $modulePath . DS . 'controllers',
            $modulePath . DS . 'views'
        ];

        foreach ($paths as $path) {
            if (!$this->getFs()->exists($path)) {
                throw new GeneratorException("Directory `$path` is not exists");
            }
        }
    }
}
