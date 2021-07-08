<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Tests\Command\Generate;

use Bluzman\Command\Generate;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Pavel Machekhin
 * @created 2014-07-10 15:04
 */
class ModuleCommandTest extends AbstractCommandTest
{
    /**
     * @var string
     */
    protected $moduleName;

    /**
     * {@inheritDoc}
     *
     * @throws \Mockery\Exception\RuntimeException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->moduleName = uniqid('m');
    }

    public function testCorrectWorkflow()
    {
        $command = new Generate\ModuleCommand();

        $this->getApplication()->addCommands([$command]);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'module' => $this->moduleName
            ]
        );

        // check that all needed folders were created
        $command->verify(
            $commandTester->getInput(), $commandTester->getOutput()
        );

        $display = $commandTester->getDisplay();

        // check all messages were displayed
        self::assertMatchesRegularExpression('/Running generate:module command/', $display);
        self::assertMatchesRegularExpression('/has been successfully created/', $display);
    }
}
