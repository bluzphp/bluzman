<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Tests\Command\Generate;

use Bluzman\Command\Generate;

use Faker;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Pavel Machekhin
 * @created 2014-07-10 15:04
 */
class ControllerCommandTest extends AbstractCommandTest
{
    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $module;

    /**
     * @var string
     */
    protected $modulePath;

    public function setUp()
    {
        parent::setUp();

        $container = new \Mockery\Container;

        $app = $container->mock('\Bluzman\Application\Application[getWorkingPath]')
            ->shouldDeferMissing()
            ->shouldAllowMockingProtectedMethods();

        $app->shouldReceive('getWorkingPath')
            ->atLeast(1)
            ->andReturn($this->workingPath)
            ->getMock();

        $this->setApplication($app);

        $this->module = $this->getFaker()->lexify();
        $this->controller = $this->getFaker()->lexify();
        $this->modulePath = $this->workingPath
            . DS . 'application'
            . DS . 'modules'
            . DS . $this->module;

        $this->getFs()->mkdir(
            $this->modulePath . DS . 'controllers'
        );
    }

    public function testCorrectWorkflow()
    {
        $command = new Generate\ControllerCommand();

        $this->getApplication()->addCommands([$command]);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'module' => $this->module,
                'controller' => $this->controller,
            ]
        );

        // check that all went well
        $command->verify($commandTester->getInput(), $commandTester->getOutput());

        $display = $commandTester->getDisplay();

        // check all messages were displayed
        self::assertRegExp('/Running generate:controller command/', $display);
        self::assertRegExp('/has been successfully created/', $display);
        self::assertFileExists(
            $this->modulePath . DS . 'controllers'
            . DS . $this->controller . '.php'
        );
    }
}
