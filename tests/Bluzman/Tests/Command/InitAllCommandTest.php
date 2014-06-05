<?php
/**
 * @author bashmach
 * @created 2014-01-05 00:10
 */

namespace Bluzman\Tests\Command;

use Bluzman\Command\Init;
use Bluzman\Application\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class InitAllCommandTest extends \PHPUnit_Framework_TestCase
{
    protected $app;

    public function setUp()
    {
        $this->app = new Application();
    }

    public function tearDown()
    {
        m::close();

        if (is_dir(PATH_TMP . DS . 'test' . DS . '.bluzman')) {
            rmdir(PATH_TMP . DS . 'test' . DS . '.bluzman');
        }

        if (is_file(PATH_TMP . DS . 'test' . DS . 'stub')) {
            unlink(PATH_TMP . DS . 'test' . DS . 'stub');
        }

        if (is_dir(PATH_TMP . DS . 'test')) {
            rmdir(PATH_TMP . DS . 'test');
        }
    }

    /**
     * @expectedException \Bluzman\Input\InputException
     */
    public function testExecuteWithoutParams()
    {
        $command = new Init\AllCommand;

        $this->app->addCommands([$command]);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            ['command' => $command->getName()],
            ['interactive' => false]
        );
    }

    /**
     * @expectedException \Bluzman\Input\InputException
     * @expectedExceptionMessage "test me now" must not contain whitespace
     */
    public function testNameOptionWithSpaces()
    {
        $command = new Init\AllCommand;

        $this->app->addCommands([$command]);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            ['command' => $command->getName(), '--name' => 'test me now'],
            ['interactive' => false]
        );
    }

    /**
     * @expectedException \Bluzman\Input\InputException
     * @expectedExceptionMessage "/root" must be writable
     */
    public function testNotWritablePathException()
    {
        $command = new Init\AllCommand;

        $this->app->addCommands([$command]);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            ['command' => $command->getName(), '--name' => 'test', '--path' => '/root'],
            ['interactive' => false]
        );
    }

    /**
     * @expectedException \Bluzman\Input\InputException
     * @expectedExceptionMessage "/somepath" must be a directory
     */
    public function testNotExistsPathException()
    {
        $command = new Init\AllCommand;

        $this->app->addCommands([$command]);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            ['command' => $command->getName(), '--name' => 'test', '--path' => '/somepath'],
            ['interactive' => false]
        );
    }

    /**
     * @expectedException \Bluzman\Input\InputException
     * @expectedExceptionMessage /test" must be empty
     */
    public function testNotEmptyPathException()
    {
        mkdir(PATH_TMP . DS . 'test');

        touch(PATH_TMP . DS . 'test' . DS . 'stub');

        $command = new Init\AllCommand;

        $this->app->addCommands([$command]);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            ['command' => $command->getName(), '--name' => 'test', '--path' => PATH_TMP],
            ['interactive' => false]
        );
    }

    /**
     *
     */
    public function testWithCorrectParams()
    {
        $container = new \Mockery\Container;

        $commandMock = $container->mock('\Bluzman\Command\Init\AllCommand[getCmdPattern]', ['init:all'])
            ->shouldDeferMissing()
            ->shouldAllowMockingProtectedMethods();

        $commandMock->shouldReceive('getCmdPattern')
            ->atLeast(1)
            ->andReturn('mkdir %s')
            ->getMock();

        $configMock = $container->mock('\Bluzman\Application\Config[putOptions]', [$this->app])
            ->shouldReceive('putOptions')
            ->atLeast(1)
            ->andReturn(true)
            ->getMock();

        $this->assertInstanceOf('\Bluzman\Command\Init\AllCommand', $commandMock);
        $this->assertInstanceOf('\Bluzman\Application\Config', $configMock);

        $command = $commandMock;
        $commandTester = new CommandTester($command);

        $this->app->addCommands([$command]);
        $this->app->setConfig($configMock);

        $commandTester->execute([
            'command' => $command->getName(),
            '--name' => 'test',
            '--path' => PATH_TMP
        ]);

        $display = $commandTester->getDisplay();

        $this->assertRegExp('/Running "init:all" command/', $display);
        $this->assertRegExp('/Cloning skeleton project/', $display);
        $this->assertRegExp('/has been successfully initialized/', $display);
    }
}