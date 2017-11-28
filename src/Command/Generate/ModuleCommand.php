<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Generate;

use Bluzman\Generator\GeneratorException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate Module Structure
 *
 * @package  Bluzman\Command\Generate
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
        $this->addForceOption();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->write('Running <info>generate:module</info> command');
        try {
            // validate
            $this->validateModuleArgument();

            // create main folder and subfolders
            $this->generate($input, $output);

            // verify it
            $this->verify($input, $output);
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
        $path = $this->getApplication()->getModulePath($input->getArgument('module'));
        $this->createSubFolders(
            $path,
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
    protected function createSubFolders($path, array $subFolders = [])
    {
        if (!$this->getFs()->exists($path)) {
            $this->getFs()->mkdir($path);
        }

        foreach ($subFolders as $subFolderName) {
            $subFolderPath = $path . DIRECTORY_SEPARATOR . $subFolderName;
            if ($this->getFs()->exists($subFolderPath)) {
                $this->comment(" |> Directory <info>$subFolderPath</info> already exists");
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
    public function verify(InputInterface $input, OutputInterface $output) : void
    {
        $module = $input->getArgument('module');
        $modulePath = $this->getApplication()->getModulePath($module);

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

        $this->write(" |> Module <info>$module</info> has been successfully created.");
    }
}
