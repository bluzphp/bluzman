<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Tests\Command\Init;

use Bluzman\Command\Init;

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
    protected $name;

    /**
     * @var string
     */
    protected $module;

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
        $this->name = $this->getFaker()->lexify();
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
        $command = new Init\ControllerCommand();

        $this->getApplication()->addCommands([$command]);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                '--name' => $this->name,
                '--module' => $this->module
            ]
        );

        // check that all went well
        $this->assertTrue($command->verify());

        $display = $commandTester->getDisplay();

        // check all messages were displayed
        $this->assertRegExp('/Running "init:controller" command/', $display);
        $this->assertRegExp('/has been successfully created/', $display);
//        $this->assertFileEquals()
        $this->assertFileExists(
            $this->modulePath . DS . 'controllers'
            . DS . $this->name . '.php'
        );
    }
}
